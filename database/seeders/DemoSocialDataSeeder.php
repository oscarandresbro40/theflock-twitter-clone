<?php

namespace Database\Seeders;

use App\Models\Follow;
use App\Models\Like;
use App\Models\Tweet;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class DemoSocialDataSeeder extends Seeder
{
    public function run(): void
    {
        $demoUser = User::query()->firstOrCreate(
            ['email' => 'demo@example.com'],
            [
                'name' => 'Demo User',
                'username' => 'demo_user',
                'email_verified_at' => now(),
                'password' => 'password',
            ]
        );

        if (is_null($demoUser->email_verified_at)) {
            $demoUser->forceFill(['email_verified_at' => now()])->save();
        }

        $users = User::factory()
            ->count(10)
            ->create();

        $allUsers = (new Collection([$demoUser]))->merge($users);

        $demoTweets = [
            'Building this Laravel Twitter clone one feature at a time.',
            'Seed data is ready. Timeline feels alive now.',
            'Following developers and discovering fresh posts today.',
        ];

        foreach ($demoTweets as $body) {
            Tweet::query()->create([
                'user_id' => $demoUser->id,
                'body' => $body,
            ]);
        }

        $allUsers->each(function (User $user): void {
            Tweet::factory()
                ->count(random_int(2, 6))
                ->for($user)
                ->create();
        });

        $this->seedFollows($allUsers, $demoUser);
        $this->seedLikes($allUsers);
    }

    private function seedFollows(Collection $users, User $demoUser): void
    {
        $users->each(function (User $follower) use ($users): void {
            $candidates = $users
                ->where('id', '!=', $follower->id)
                ->pluck('id')
                ->all();

            if ($candidates === []) {
                return;
            }

            $targetCount = min(count($candidates), random_int(2, 5));
            $followedIds = fake()->randomElements($candidates, $targetCount);

            foreach ((array) $followedIds as $followedId) {
                Follow::query()->firstOrCreate([
                    'follower_id' => $follower->id,
                    'followed_id' => $followedId,
                ]);
            }
        });

        $demoMustFollow = $users
            ->where('id', '!=', $demoUser->id)
            ->take(4)
            ->pluck('id');

        foreach ($demoMustFollow as $followedId) {
            Follow::query()->firstOrCreate([
                'follower_id' => $demoUser->id,
                'followed_id' => $followedId,
            ]);
        }
    }

    private function seedLikes(Collection $users): void
    {
        $tweetIdsByUser = Tweet::query()
            ->get(['id', 'user_id'])
            ->groupBy('user_id')
            ->map(fn (Collection $tweets): array => $tweets->pluck('id')->all());

        $allTweetIds = Tweet::query()->pluck('id')->all();

        $users->each(function (User $user) use ($tweetIdsByUser, $allTweetIds): void {
            $ownTweetIds = $tweetIdsByUser->get($user->id, []);
            $likeCandidates = array_values(array_diff($allTweetIds, $ownTweetIds));

            if ($likeCandidates === []) {
                return;
            }

            $targetCount = min(count($likeCandidates), random_int(4, 12));
            $likedTweetIds = fake()->randomElements($likeCandidates, $targetCount);

            foreach ((array) $likedTweetIds as $tweetId) {
                Like::query()->firstOrCreate([
                    'user_id' => $user->id,
                    'tweet_id' => $tweetId,
                ]);
            }
        });
    }
}
