<?php

namespace App\Filament\Widgets;

use App\Models\Task;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Employees', User::where('role', User::ROLE_EMPLOYER)->count())
                ->description('Active employees')
                ->color('primary'),
            Stat::make('Pending', Task::where('status', 'todo')->count())
                ->description('Todo tasks')
                ->color('gray'),
            Stat::make('In Progress', Task::where('status', 'in_progress')->count())
                ->description('Work in progress')
                ->color('info'),
            Stat::make('Done', Task::where('status', 'done')->count())
                ->description('Completed work')
                ->color('success'),
        ];
    }
}
