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

        // 3. Tasklar yaratish
        $allUsers = User::all();

        // Ali Valiyev uchun maxsus har bir statusdan 5 tadan task yaratish
        foreach (Task::STATUSES as $status) {
            Task::factory(5)->create([
                'user_id' => $testEmployee->id,
                'created_by' => $admin->id,
                'status' => $status,
                'start_date' => now()->subDays(rand(1, 10)),
            ]);
        }

        // Qolgan xodimlar uchun oddiygina tasklar yaratish
        foreach ($employees as $emp) {
            if ($emp->id === $testEmployee->id) continue;

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