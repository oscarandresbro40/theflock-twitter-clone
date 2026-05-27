<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_authenticated_user_can_update_own_username_and_bio(): void
    {
        $user = User::factory()->create([
            'username' => 'oldusername',
            'bio' => 'Old bio',
        ]);

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'username' => 'newusername',
                'bio' => 'New bio text',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('newusername', $user->username);
        $this->assertSame('New bio text', $user->bio);
    }

    public function test_username_must_be_unique(): void
    {
        User::factory()->create(['username' => 'takenname']);
        $user = User::factory()->create(['username' => 'freetoname']);

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->patch('/profile', [
                'username' => 'takenname',
                'bio' => 'Any bio',
            ]);

        $response
            ->assertSessionHasErrors('username')
            ->assertRedirect('/profile');

        $this->assertSame('freetoname', $user->fresh()->username);
    }

    public function test_bio_can_not_exceed_160_characters(): void
    {
        $user = User::factory()->create(['username' => 'biotester']);

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->patch('/profile', [
                'username' => 'biotester',
                'bio' => str_repeat('a', 161),
            ]);

        $response
            ->assertSessionHasErrors('bio')
            ->assertRedirect('/profile');
    }
}
