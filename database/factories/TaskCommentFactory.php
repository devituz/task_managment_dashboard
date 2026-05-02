<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TaskCommentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'comment' => fake()->realText(200),
            'created_at' => fake()->dateTimeBetween('-1 month', 'now'),
        ];
    }
}