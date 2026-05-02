<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\TaskComment;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Superadmin yaratish
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password'),
                'role' => User::ROLE_SUPERADMIN,
            ]
        );

        // 2. Oddiy Employerlar (Xodimlar) yaratish (10 ta)
        $employees = User::factory(10)->create([
            'role' => User::ROLE_EMPLOYER,
            'password' => bcrypt('password'),
        ]);

        // Qo'shimcha bitta test employer: employee@example.com (agar oldin bo'lmagan bo'lsa)
        $testEmployee = User::firstOrCreate(
            ['email' => 'employee@example.com'],
            [
                'name' => 'Ali Valiyev',
                'password' => bcrypt('password'),
                'role' => User::ROLE_EMPLOYER,
            ]
        );
        $employees->push($testEmployee);

        // 3. Tasklar yaratish (Har bir employee uchun o'rtacha 5-8 ta task)
        $allUsers = User::all();

        foreach ($employees as $emp) {
            $tasks = Task::factory(rand(5, 8))->create([
                'user_id' => $emp->id,
                'created_by' => $admin->id,
            ]);

            // Har bir task uchun tasodifiy izohlar (Comments) yozish
            foreach ($tasks as $task) {
                if (rand(0, 1)) { // 50% ehtimollik bilan
                    TaskComment::factory(rand(1, 4))->create([
                        'task_id' => $task->id,
                        'user_id' => $allUsers->random()->id,
                    ]);
                }
            }
        }
    }
}