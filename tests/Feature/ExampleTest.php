<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_root_redirects_to_admin_panel(): void
    {
        $this->get('/')->assertRedirect('/admin');
    }

    public function test_admin_can_access_filament_dashboard(): void
    {
        $admin = User::factory()->superadmin()->create();

        $this->actingAs($admin)
            ->get('/admin')
            ->assertOk();
    }

    public function test_superadmin_can_create_employee(): void
    {
        $admin = User::factory()->superadmin()->create();

        $this->actingAs($admin)
            ->post(route('employees.store'), [
                'name' => 'New Employee',
                'email' => 'new@example.com',
                'password' => 'password',
                'telegram_id' => '12345',
            ])
            ->assertRedirect(route('employees.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'new@example.com',
            'role' => User::ROLE_EMPLOYER,
            'telegram_id' => '12345',
        ]);
    }

    public function test_employee_can_move_task_forward_but_cannot_complete_it(): void
    {
        $employee = User::factory()->create();
        $task = Task::create([
            'title' => 'Dashboard UI',
            'user_id' => $employee->id,
            'priority' => 'high',
            'status' => 'todo',
        ]);

        $this->actingAs($employee)
            ->patch(route('tasks.status', $task), ['status' => 'in_progress'])
            ->assertRedirect();

        $this->assertSame('in_progress', $task->fresh()->status);

        $this->actingAs($employee)
            ->patch(route('tasks.status', $task), ['status' => 'complete'])
            ->assertStatus(422);
    }
}
