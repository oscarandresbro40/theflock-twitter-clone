<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserFollowListTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_can_view_followers_list(): void
    {
        $target = User::factory()->create(['username' => 'target']);
        $follower = User::factory()->create(['username' => 'follower']);
        $follower->following()->attach($target, [
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->get(route('users.followers', $target));

        $response->assertOk();
        $response->assertSee('follower');
    }

    public function test_guests_can_view_following_list(): void
    {
        $target = User::factory()->create(['username' => 'target']);
        $followed = User::factory()->create(['username' => 'followed']);
        $target->following()->attach($followed, [
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->get(route('users.following', $target));

        $response->assertOk();
        $response->assertSee('followed');
    }

    public function test_followers_are_ordered_by_follow_created_at_descending(): void
    {
        $target = User::factory()->create(['username' => 'target']);
        $olderFollower = User::factory()->create(['username' => 'olderfollower']);
        $newerFollower = User::factory()->create(['username' => 'newerfollower']);

        $olderFollower->following()->attach($target, [
            'created_at' => now()->subMinute(),
            'updated_at' => now()->subMinute(),
        ]);

        $newerFollower->following()->attach($target, [
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->get(route('users.followers', $target));

        $response->assertOk();
        $response->assertSeeInOrder(['newerfollower', 'olderfollower']);
    }

    public function test_following_users_are_ordered_by_follow_created_at_descending(): void
    {
        $target = User::factory()->create(['username' => 'target']);
        $olderFollowed = User::factory()->create(['username' => 'olderfollowed']);
        $newerFollowed = User::factory()->create(['username' => 'newerfollowed']);

        $target->following()->attach($olderFollowed, [
            'created_at' => now()->subMinute(),
            'updated_at' => now()->subMinute(),
        ]);

        $target->following()->attach($newerFollowed, [
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->get(route('users.following', $target));

        $response->assertOk();
        $response->assertSeeInOrder(['newerfollowed', 'olderfollowed']);
    }

    public function test_authenticated_users_see_follow_and_unfollow_controls(): void
    {
        $viewer = User::factory()->create(['username' => 'viewer']);
        $target = User::factory()->create(['username' => 'target']);
        $followedUser = User::factory()->create(['username' => 'followeduser']);
        $unfollowedUser = User::factory()->create(['username' => 'unfolloweduser']);

        $followedUser->following()->attach($target, [
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $unfollowedUser->following()->attach($target, [
            'created_at' => now()->subSecond(),
            'updated_at' => now()->subSecond(),
        ]);

        $viewer->following()->attach($followedUser, [
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this
            ->actingAs($viewer)
            ->get(route('users.followers', $target));

        $response->assertOk();
        $response->assertSee('Unfollow');
        $response->assertSee('Follow');
        $response->assertDontSee('Log in to follow');
    }

    public function test_guests_see_login_links_instead_of_follow_controls(): void
    {
        $target = User::factory()->create(['username' => 'target']);
        $follower = User::factory()->create(['username' => 'follower']);
        $follower->following()->attach($target, [
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->get(route('users.followers', $target));

        $response->assertOk();
        $response->assertSee('Log in to follow');
        $response->assertDontSee('>Follow<', false);
        $response->assertDontSee('Unfollow');
    }
}
