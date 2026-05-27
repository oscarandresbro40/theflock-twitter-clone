<?php

namespace Tests\Feature;

use App\Models\Tweet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TweetReplyThreadTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_can_view_a_tweet_thread(): void
    {
        $parent = Tweet::factory()->create([
            'body' => 'parent-thread-body',
        ]);

        Tweet::factory()->create([
            'parent_id' => $parent->id,
            'body' => 'reply-thread-body',
        ]);

        $response = $this->get(route('tweets.show', $parent));

        $response->assertOk();
        $response->assertSee('parent-thread-body');
        $response->assertSee('reply-thread-body');
        $response->assertSee('Log in');
    }

    public function test_authenticated_users_can_reply_to_a_tweet(): void
    {
        $parent = Tweet::factory()->create();
        $replier = User::factory()->create();

        $response = $this
            ->actingAs($replier)
            ->post(route('tweets.replies.store', $parent), [
                'body' => 'new-auth-reply-body',
            ]);

        $response->assertRedirect(route('tweets.show', $parent));

        $this->assertDatabaseHas('tweets', [
            'user_id' => $replier->id,
            'parent_id' => $parent->id,
            'body' => 'new-auth-reply-body',
        ]);
    }

    public function test_guests_cannot_reply_to_tweets(): void
    {
        $parent = Tweet::factory()->create();

        $response = $this->post(route('tweets.replies.store', $parent), [
            'body' => 'guest-reply-body',
        ]);

        $response->assertRedirect('/login');
        $this->assertDatabaseMissing('tweets', [
            'body' => 'guest-reply-body',
        ]);
    }

    public function test_replies_are_shown_under_the_parent_tweet(): void
    {
        $parent = Tweet::factory()->create([
            'body' => 'thread-parent-body',
        ]);

        Tweet::factory()->create([
            'parent_id' => $parent->id,
            'body' => 'thread-reply-body',
        ]);

        $response = $this->get(route('tweets.show', $parent));

        $response->assertOk();
        $response->assertSeeInOrder(['thread-parent-body', 'thread-reply-body']);
    }

    public function test_replies_are_ordered_by_created_at_ascending(): void
    {
        $parent = Tweet::factory()->create();

        $olderReply = Tweet::factory()->create([
            'parent_id' => $parent->id,
            'body' => 'older-reply-body',
            'created_at' => now()->subMinute(),
        ]);

        $newerReply = Tweet::factory()->create([
            'parent_id' => $parent->id,
            'body' => 'newer-reply-body',
            'created_at' => now(),
        ]);

        $response = $this->get(route('tweets.show', $parent));

        $response->assertOk();
        $response->assertSeeInOrder([$olderReply->body, $newerReply->body]);
    }

    public function test_timeline_excludes_replies(): void
    {
        $user = User::factory()->create();

        $rootTweet = Tweet::factory()->for($user)->create([
            'body' => 'timeline-root-body',
        ]);

        Tweet::factory()->for($user)->create([
            'parent_id' => $rootTweet->id,
            'body' => 'timeline-reply-body',
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('timeline-root-body');
        $response->assertDontSee('timeline-reply-body');
    }

    public function test_profile_tweet_list_excludes_replies(): void
    {
        $profileUser = User::factory()->create();

        $rootTweet = Tweet::factory()->for($profileUser)->create([
            'body' => 'profile-root-body',
        ]);

        Tweet::factory()->for($profileUser)->create([
            'parent_id' => $rootTweet->id,
            'body' => 'profile-reply-body',
        ]);

        $response = $this->get(route('users.show', $profileUser));

        $response->assertOk();
        $response->assertSee('profile-root-body');
        $response->assertDontSee('profile-reply-body');
    }

    public function test_deleting_parent_tweet_keeps_replies_safe(): void
    {
        $owner = User::factory()->create();
        $replyAuthor = User::factory()->create();

        $parent = Tweet::factory()->for($owner)->create();

        $reply = Tweet::factory()->for($replyAuthor)->create([
            'parent_id' => $parent->id,
            'body' => 'safe-reply-body',
        ]);

        $response = $this
            ->actingAs($owner)
            ->delete(route('tweets.destroy', $parent));

        $response->assertRedirect(route('dashboard'));

        $this->assertDatabaseMissing('tweets', [
            'id' => $parent->id,
        ]);

        $this->assertDatabaseHas('tweets', [
            'id' => $reply->id,
            'parent_id' => null,
            'body' => 'safe-reply-body',
        ]);
    }
}
