@extends('layouts.app')

@section('title', __('app.dashboard'))

@section('content')
    <div class="row g-4 mb-5">
        @if(! is_null($employeeCount))
            <div class="col-xl-3 col-sm-6">
                <div class="card border-0 p-3 h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-indigo-soft text-primary rounded-4 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; background: #eef2ff;">
                            <i class="bi bi-people fs-4"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-medium text-uppercase">{{ __('app.total_employees') }}</div>
                            <h3 class="mb-0 fw-bold">{{ $employeeCount }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-blue-soft text-info rounded-4 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; background: #f0f9ff;">
                        <i class="bi bi-journal-check fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium text-uppercase">{{ __('app.total_tasks') }}</div>
                        <h3 class="mb-0 fw-bold">{{ $totalTasks }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card p-3 h-100 border-start border-danger border-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-4 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                        <i class="bi bi-clock-history fs-4"></i>
                    </div>
                    <div>
                        <div class="text-danger small fw-bold text-uppercase">{{ __('app.overdue_tasks') }}</div>
                        <h3 class="mb-0 fw-bold">{{ $overdueCount }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-12">
            <h6 class="fw-bold mb-3 text-muted text-uppercase small">{{ __('app.task_pipeline') }}</h6>
            <div class="row g-3">
                @php
                    $statusConfig = [
                        'todo' => ['bg' => 'rgba(100, 116, 139, 0.1)', 'text' => '#64748b', 'icon' => 'bi-circle'],
                        'in_progress' => ['bg' => 'rgba(59, 130, 246, 0.1)', 'text' => '#3b82f6', 'icon' => 'bi-play-circle'],
                        'testing' => ['bg' => 'rgba(245, 158, 11, 0.1)', 'text' => '#f59e0b', 'icon' => 'bi-patch-check'],
                        'done' => ['bg' => 'rgba(16, 185, 129, 0.1)', 'text' => '#10b981', 'icon' => 'bi-check-circle'],
                        'complete' => ['bg' => 'rgba(99, 102, 241, 0.1)', 'text' => '#6366f1', 'icon' => 'bi-flag-fill'],
                    ];
                @endphp
                @foreach($statusCounts as $status => $count)
                    <div class="col-md col-sm-6">
                        <a href="{{ route('tasks.index', ['status' => $status]) }}" class="text-decoration-none">
                            <div class="card text-center p-3 h-100 transition-all hover-lift" style="background: {{ $statusConfig[$status]['bg'] }}; border: 1px solid {{ $statusConfig[$status]['bg'] }};">
                                <div class="small fw-bold mb-2" style="color: {{ $statusConfig[$status]['text'] }};">
                                    <i class="bi {{ $statusConfig[$status]['icon'] }} me-1"></i>
                                    {{ \App\Models\Task::statusLabel($status) }}
                                </div>
                                <h2 class="mb-0 fw-bold" style="color: {{ $statusConfig[$status]['text'] }};">{{ $count }}</h2>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card overflow-hidden">
                <div class="card-header py-3 px-4 border-bottom" style="background: var(--card-bg);">
                    <h6 class="mb-0 fw-bold">{{ __('app.recent_tasks') }}</h6>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>{{ __('app.task_name') }}</th>
                                <th>{{ __('app.priority') }}</th>
                                <th>{{ __('app.employee') }}</th>
                                <th class="text-end">{{ __('app.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentTasks as $task)
                            <tr>
                                <td class="px-4">
                                    <div class="fw-semibold text-body">{{ $task->title }}</div>
                                    <small class="text-muted">{{ $task->created_at->diffForHumans() }}</small>
                                </td>
                                <td>
                                    @php $p = ['low'=>'success', 'medium'=>'warning', 'high'=>'danger'][$task->priority]; @endphp
                                    <span class="badge bg-{{ $p }} bg-opacity-10 text-{{ $p }}">{{ strtoupper($task->priority) }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center small" style="width: 28px; height: 28px; font-weight: 700; background: var(--input-bg);">{{ substr($task->employee->name, 0, 1) }}</div>
                                        <span class="small">{{ $task->employee->name }}</span>
                                    </div>
                                </td>
                                <td class="text-end px-4">
                                    <a href="{{ route('tasks.show', $task) }}" class="btn btn-sm btn-light border-0"><i class="bi bi-chevron-right"></i></a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <style>
        .hover-lift:hover { transform: translateY(-4px); box-shadow: 0 10px 20px -5px rgba(0,0,0,0.05) !important; }
        .transition-all { transition: all 0.25s ease; }
    </style>
@endsection
