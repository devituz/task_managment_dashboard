<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $tasksQuery = Task::query();

        if (! $user->isSuperadmin()) {
            $tasksQuery->where('user_id', $user->id);
        }

        $statusCounts = Task::STATUSES;
        $counts = (clone $tasksQuery)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $overdueCount = (clone $tasksQuery)
            ->whereNotNull('deadline')
            ->where('deadline', '<', now()->toDateString())
            ->where('status', '!=', 'complete')
            ->count();

        $recentTasks = (clone $tasksQuery)
            ->with('employee')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.index', [
            'employeeCount' => $user->isSuperadmin() ? User::where('role', User::ROLE_EMPLOYER)->count() : null,
            'totalTasks' => $counts->sum(),
            'statusCounts' => collect($statusCounts)->mapWithKeys(fn ($status) => [$status => (int) ($counts[$status] ?? 0)]),
            'overdueCount' => $overdueCount,
            'recentTasks' => $recentTasks,
        ]);
    }
}
