<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_can_view_search_results(): void
    {
        User::factory()->create(['username' => 'alice']);

        $response = $this->get(route('users.search', ['q' => 'ali']));

        $response->assertOk();
        $response->assertSee('alice');
    }

    public function test_users_can_search_by_username_prefix(): void
    {
        User::factory()->create(['username' => 'anna']);
        User::factory()->create(['username' => 'andrew']);
        User::factory()->create(['username' => 'brian']);

        $response = $this->get(route('users.search', ['q' => 'an']));

        $response->assertOk();
        $response->assertSee('anna');
        $response->assertSee('andrew');
        $response->assertDontSee('brian');
    }

    public function test_results_are_ordered_by_username_ascending(): void
    {
        User::factory()->create(['username' => 'zoe']);
        User::factory()->create(['username' => 'zara']);
        User::factory()->create(['username' => 'zed']);

        $response = $this->get(route('users.search', ['q' => 'z']));

        $response->assertOk();
        $response->assertSeeInOrder(['zara', 'zed', 'zoe']);
    }

    public function test_authenticated_users_see_follow_and_unfollow_controls(): void
    {
        $viewer = User::factory()->create(['username' => 'viewer']);
        $followedUser = User::factory()->create(['username' => 'alice']);
        $unfollowedUser = User::factory()->create(['username' => 'alex']);
        $viewer->following()->attach($followedUser);

        $response = $this
            ->actingAs($viewer)
            ->get(route('users.search', ['q' => 'a']));

        $response->assertOk();
        $response->assertSee('Unfollow');
        $response->assertSee('Follow');
        $response->assertDontSee('Log in to follow');
    }

    public function test_guests_see_login_links_instead_of_follow_controls(): void
    {
        User::factory()->create(['username' => 'amanda']);

        $response = $this->get(route('users.search', ['q' => 'am']));

        $response->assertOk();
        $response->assertSee('Log in to follow');
        $response->assertDontSee('>Follow<', false);
        $response->assertDontSee('Unfollow');
    }
}
