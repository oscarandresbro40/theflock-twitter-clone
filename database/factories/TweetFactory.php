<?php

namespace Database\Factories;

use App\Models\Tweet;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tweet>
 */
class TweetFactory extends Factory
{
    protected $model = Tweet::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'body' => fake()->realTextBetween(20, 120),
        ];
    }
}