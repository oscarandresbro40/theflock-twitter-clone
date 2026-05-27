<?php

namespace Tests\Feature;

use App\Models\Tweet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class TimelineTest extends TestCase
{
    use RefreshDatabase;

    public function test_timeline_includes_the_authenticated_users_tweets(): void
    {
        $user = User::factory()->create();

        $ownTweet = Tweet::factory()->for($user)->create([
            'body' => 'My own timeline tweet',
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee($ownTweet->body);
    }

    public function test_timeline_includes_followed_users_tweets(): void
    {
        $user = User::factory()->create();
        $followedUser = User::factory()->create();
        $user->following()->attach($followedUser);

        $followedTweet = Tweet::factory()->for($followedUser)->create([
            'body' => 'Followed user timeline tweet',
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee($followedTweet->body);
    }

    public function test_timeline_excludes_tweets_from_users_not_followed(): void
    {
        $user = User::factory()->create();
        $notFollowedUser = User::factory()->create();

        $notFollowedTweet = Tweet::factory()->for($notFollowedUser)->create([
            'body' => 'Not followed user tweet',
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('dashboard'));

        $response->assertOk();
        $response->assertDontSee($notFollowedTweet->body);
    }

    public function test_timeline_is_ordered_by_created_at_descending(): void
    {
        $user = User::factory()->create();
        $followedUser = User::factory()->create();
        $user->following()->attach($followedUser);

        $oldestTweet = Tweet::factory()->for($user)->create([
            'body' => 'Oldest timeline tweet',
            'created_at' => now()->subMinutes(2),
        ]);

        $newestTweet = Tweet::factory()->for($followedUser)->create([
            'body' => 'Newest timeline tweet',
            'created_at' => now(),
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('dashboard'));

        $response->assertOk();
        $response->assertSeeInOrder([$newestTweet->body, $oldestTweet->body]);
    }

    public function test_timeline_is_paginated(): void
    {
        $user = User::factory()->create();

        for ($i = 1; $i <= 11; $i++) {
            Tweet::factory()->for($user)->create([
                'body' => sprintf('timeline-body-%04d', $i),
                'created_at' => now()->subMinutes(11 - $i),
            ]);
        }

        $firstPageResponse = $this
            ->actingAs($user)
            ->get(route('dashboard'));

        $firstPageResponse->assertOk();
        $firstPageResponse->assertSee('timeline-body-0011');
        $firstPageResponse->assertDontSee('timeline-body-0001');
        $firstPageResponse->assertViewHas('tweets', function ($tweets): bool {
            return $tweets instanceof LengthAwarePaginator
                && $tweets->perPage() === 10
                && $tweets->total() === 11;
        });

        $secondPageResponse = $this
            ->actingAs($user)
            ->get(route('dashboard', ['page' => 2]));

        $secondPageResponse->assertOk();
        $secondPageResponse->assertSee('timeline-body-0001');
        $secondPageResponse->assertDontSee('timeline-body-0011');
    }
}
