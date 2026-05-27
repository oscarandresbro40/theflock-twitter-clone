<?php

namespace Tests\Feature;

use App\Models\Tweet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LikeTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_users_can_like_a_tweet(): void
    {
        $user = User::factory()->create();
        $tweet = Tweet::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('likes.store', $tweet));

        $response->assertRedirect();

        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'tweet_id' => $tweet->id,
        ]);
    }

    public function test_authenticated_users_can_unlike_a_tweet(): void
    {
        $user = User::factory()->create();
        $tweet = Tweet::factory()->create();
        $user->likedTweets()->attach($tweet);

        $response = $this
            ->actingAs($user)
            ->delete(route('likes.destroy', $tweet));

        $response->assertRedirect();

        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'tweet_id' => $tweet->id,
        ]);
    }

    public function test_guests_cannot_like_tweets(): void
    {
        $tweet = Tweet::factory()->create();

        $response = $this->post(route('likes.store', $tweet));

        $response->assertRedirect('/login');
        $this->assertDatabaseCount('likes', 0);
    }

    public function test_duplicate_likes_are_idempotent(): void
    {
        $user = User::factory()->create();
        $tweet = Tweet::factory()->create();

        $this->actingAs($user)->post(route('likes.store', $tweet));
        $response = $this->actingAs($user)->post(route('likes.store', $tweet));

        $response->assertRedirect();

        $this->assertDatabaseCount('likes', 1);
    }

    public function test_like_count_is_visible_on_the_dashboard(): void
    {
        $user = User::factory()->create();
        $tweet = Tweet::factory()->for($user)->create([
            'body' => 'Tweet with visible likes',
        ]);

        $otherUsers = User::factory()->count(2)->create();
        $tweet->likedByUsers()->attach($otherUsers->modelKeys());

        $response = $this
            ->actingAs($user)
            ->get('/dashboard');

        $response->assertOk();
        $response->assertSee('2 likes');
        $response->assertSee('Tweet with visible likes');
    }
}