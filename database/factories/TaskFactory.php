<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'description' => '<p>' . implode('</p><p>', fake()->paragraphs(3)) . '</p>',
            // user_id va created_by seeder ichidan beriladi
            'priority' => fake()->randomElement(Task::PRIORITIES),
            'status' => fake()->randomElement(Task::STATUSES),
            'deadline' => fake()->boolean(80) ? fake()->dateTimeBetween('-10 days', '+30 days') : null,
            'created_at' => fake()->dateTimeBetween('-2 months', 'now'),
        ];
    }
}