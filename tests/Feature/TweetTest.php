<?php

namespace Tests\Feature;

use App\Models\Tweet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TweetTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_users_can_create_tweets(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post('/tweets', [
                'body' => 'Posting my first tweet.',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/dashboard');

        $this->assertDatabaseHas('tweets', [
            'user_id' => $user->id,
            'body' => 'Posting my first tweet.',
        ]);
    }

    public function test_authenticated_users_can_create_a_tweet_with_an_image(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post('/tweets', [
                'body' => 'Tweet with an image',
                'image' => UploadedFile::fake()->image('tweet.jpg', 800, 600),
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/dashboard');

        $tweet = Tweet::query()->latest('id')->first();

        $this->assertNotNull($tweet);
        $this->assertNotNull($tweet->image_path);
        Storage::disk('public')->assertExists($tweet->image_path);
    }

    public function test_image_is_optional_when_creating_tweets(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post('/tweets', [
                'body' => 'Tweet without an image',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/dashboard');

        $this->assertDatabaseHas('tweets', [
            'user_id' => $user->id,
            'body' => 'Tweet without an image',
            'image_path' => null,
        ]);
    }

    public function test_invalid_files_are_rejected_for_tweet_images(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/dashboard')
            ->post('/tweets', [
                'body' => 'Tweet with invalid file',
                'image' => UploadedFile::fake()->create('invalid.pdf', 200, 'application/pdf'),
            ]);

        $response
            ->assertSessionHasErrors('image')
            ->assertRedirect('/dashboard');
    }

    public function test_oversized_files_are_rejected_for_tweet_images(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/dashboard')
            ->post('/tweets', [
                'body' => 'Tweet with oversized image',
                'image' => UploadedFile::fake()->create('large.jpg', 3000, 'image/jpeg'),
            ]);

        $response
            ->assertSessionHasErrors('image')
            ->assertRedirect('/dashboard');
    }

    public function test_tweet_body_can_not_exceed_280_characters(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/dashboard')
            ->post('/tweets', [
                'body' => str_repeat('a', 281),
            ]);

        $response
            ->assertSessionHasErrors('body')
            ->assertRedirect('/dashboard');

        $this->assertDatabaseCount('tweets', 0);
    }

    public function test_guests_can_not_create_tweets(): void
    {
        $response = $this->post('/tweets', [
            'body' => 'Guests should not be able to post.',
        ]);

        $response->assertRedirect('/login');
        $this->assertDatabaseCount('tweets', 0);
    }

    public function test_guests_can_not_create_tweets_with_images(): void
    {
        Storage::fake('public');

        $response = $this->post('/tweets', [
            'body' => 'Guests should not be able to post images.',
            'image' => UploadedFile::fake()->image('guest.jpg', 640, 480),
        ]);

        $response->assertRedirect('/login');
        $this->assertDatabaseCount('tweets', 0);
    }

    public function test_authenticated_users_can_view_their_tweets_in_reverse_chronological_order(): void
    {
        $user = User::factory()->create();

        $olderTweet = Tweet::factory()->for($user)->create([
            'body' => 'Older tweet',
            'created_at' => now()->subMinute(),
        ]);

        $newerTweet = Tweet::factory()->for($user)->create([
            'body' => 'Newer tweet',
            'created_at' => now(),
        ]);

        Tweet::factory()->create([
            'body' => 'Another user tweet',
        ]);

        $response = $this
            ->actingAs($user)
            ->get('/dashboard');

        $response->assertOk();
        $response->assertSeeInOrder([$newerTweet->body, $olderTweet->body]);
        $response->assertDontSee('Another user tweet');
    }

    public function test_users_can_delete_their_own_tweets(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $imagePath = UploadedFile::fake()->image('delete-me.jpg')->store('tweet-images', 'public');

        $tweet = Tweet::factory()->for($user)->create([
            'image_path' => $imagePath,
        ]);

        $response = $this
            ->actingAs($user)
            ->delete("/tweets/{$tweet->id}");

        $response->assertRedirect('/dashboard');
        $this->assertDatabaseMissing('tweets', [
            'id' => $tweet->id,
        ]);
        Storage::disk('public')->assertMissing($imagePath);
    }

    public function test_tweet_image_is_displayed_where_expected(): void
    {
        $user = User::factory()->create();

        $tweet = Tweet::factory()->for($user)->create([
            'body' => 'Tweet with visible image',
            'image_path' => 'tweet-images/visible-image.jpg',
        ]);

        $dashboardResponse = $this
            ->actingAs($user)
            ->get('/dashboard');

        $dashboardResponse->assertOk();
        $dashboardResponse->assertSee('storage/tweet-images/visible-image.jpg');

        $profileResponse = $this->get(route('users.show', $user));
        $profileResponse->assertOk();
        $profileResponse->assertSee('storage/tweet-images/visible-image.jpg');

        $threadResponse = $this->get(route('tweets.show', $tweet));
        $threadResponse->assertOk();
        $threadResponse->assertSee('storage/tweet-images/visible-image.jpg');
    }

    public function test_users_can_not_delete_tweets_they_do_not_own(): void
    {
        $tweet = Tweet::factory()->create();
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete("/tweets/{$tweet->id}");

        $response->assertForbidden();
        $this->assertDatabaseHas('tweets', [
            'id' => $tweet->id,
        ]);
    }

    public function test_guests_can_not_delete_tweets(): void
    {
        $tweet = Tweet::factory()->create();

        $response = $this->delete("/tweets/{$tweet->id}");

        $response->assertRedirect('/login');
        $this->assertDatabaseHas('tweets', [
            'id' => $tweet->id,
        ]);
    }
}