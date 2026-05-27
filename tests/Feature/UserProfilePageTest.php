<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserProfilePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_can_view_public_profile_pages(): void
    {
        $user = User::factory()->create([
            'username' => 'publicuser',
            'bio' => 'Public bio',
        ]);

        $response = $this->get(route('users.show', $user));

        $response->assertOk();
        $response->assertSee('publicuser');
    }

    public function test_profile_displays_username_bio_follower_count_and_following_count(): void
    {
        $profileUser = User::factory()->create([
            'username' => 'profileuser',
            'bio' => 'Bio on profile page',
        ]);

        $followerA = User::factory()->create();
        $followerB = User::factory()->create();
        $followingA = User::factory()->create();

        $followerA->following()->attach($profileUser);
        $followerB->following()->attach($profileUser);
        $profileUser->following()->attach($followingA);

        $response = $this->get(route('users.show', $profileUser));

        $response->assertOk();
        $response->assertSee('profileuser');
        $response->assertSee('Bio on profile page');
        $response->assertSee('2 followers');
        $response->assertSee('1 following');
    }

    public function test_profile_tweets_are_ordered_by_created_at_descending(): void
    {
        $profileUser = User::factory()->create(['username' => 'ordereduser']);

        $olderTweet = $profileUser->tweets()->create([
            'body' => 'older tweet body',
            'created_at' => now()->subMinute(),
            'updated_at' => now()->subMinute(),
        ]);

        $newerTweet = $profileUser->tweets()->create([
            'body' => 'newer tweet body',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->get(route('users.show', $profileUser));

        $response->assertOk();
        $response->assertSeeInOrder([$newerTweet->body, $olderTweet->body]);
    }
}
