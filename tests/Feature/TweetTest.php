<?php

namespace Tests\Feature;

use App\Models\Tweet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
        $user = User::factory()->create();
        $tweet = Tweet::factory()->for($user)->create();

        $response = $this
            ->actingAs($user)
            ->delete("/tweets/{$tweet->id}");

        $response->assertRedirect('/dashboard');
        $this->assertDatabaseMissing('tweets', [
            'id' => $tweet->id,
        ]);
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