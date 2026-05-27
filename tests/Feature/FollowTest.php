<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FollowTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_users_can_follow_another_user(): void
    {
        $follower = User::factory()->create();
        $followed = User::factory()->create();

        $response = $this
            ->actingAs($follower)
            ->post(route('follows.store', $followed));

        $response->assertRedirect();

        $this->assertDatabaseHas('follows', [
            'follower_id' => $follower->id,
            'followed_id' => $followed->id,
        ]);

        $this->assertTrue($follower->fresh()->isFollowing($followed));
    }

    public function test_authenticated_users_can_unfollow_another_user(): void
    {
        $follower = User::factory()->create();
        $followed = User::factory()->create();
        $follower->following()->attach($followed);

        $response = $this
            ->actingAs($follower)
            ->delete(route('follows.destroy', $followed));

        $response->assertRedirect();

        $this->assertDatabaseMissing('follows', [
            'follower_id' => $follower->id,
            'followed_id' => $followed->id,
        ]);
    }

    public function test_guests_cannot_follow_users(): void
    {
        $followed = User::factory()->create();

        $response = $this->post(route('follows.store', $followed));

        $response->assertRedirect('/login');
        $this->assertDatabaseCount('follows', 0);
    }

    public function test_users_cannot_follow_themselves(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from(route('dashboard'))
            ->post(route('follows.store', $user));

        $response
            ->assertRedirect(route('dashboard'))
            ->assertSessionHasErrors('follow');

        $this->assertDatabaseCount('follows', 0);
    }

    public function test_duplicate_follows_are_idempotent(): void
    {
        $follower = User::factory()->create();
        $followed = User::factory()->create();

        $this->actingAs($follower)->post(route('follows.store', $followed));
        $response = $this->actingAs($follower)->post(route('follows.store', $followed));

        $response->assertRedirect();

        $this->assertDatabaseCount('follows', 1);
        $this->assertTrue($follower->fresh()->isFollowing($followed));
    }
}