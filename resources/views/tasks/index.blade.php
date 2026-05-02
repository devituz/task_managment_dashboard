@extends('layouts.app')

@section('title', __('app.tasks'))

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1">{{ __('app.task_management') }}</h4>
        <p class="text-muted small mb-0">{{ __('app.task_list_desc') }}</p>
    </div>
    @if(auth()->user()->isSuperadmin())
        <a href="{{ route('tasks.create') }}" class="btn btn-primary px-4 py-2 shadow-sm">
            <i class="bi bi-plus-lg me-2"></i> {{ __('app.new_task') }}
        </a>
    @endif
</div>

<!-- Advanced Filters -->
<div class="card mb-4 p-2">
    <div class="card-body">
        <form action="{{ route('tasks.index') }}" method="GET" class="row g-3">
            <div class="col-lg-4">
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="{{ __('app.search') }}" value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-2 col-6">
                <select name="status" class="form-select">
                    <option value="">{{ __('app.all_statuses') }}</option>
                    @foreach(\App\Models\Task::STATUSES as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ \App\Models\Task::statusLabel($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 col-6">
                <select name="priority" class="form-select">
                    <option value="">{{ __('app.all_priorities') }}</option>
                    @foreach(\App\Models\Task::PRIORITIES as $priority)
                        <option value="{{ $priority }}" @selected(request('priority') === $priority)>{{ \App\Models\Task::priorityLabel($priority) }}</option>
                    @endforeach
                </select>
            </div>
            @if(auth()->user()->isSuperadmin())
            <div class="col-md-3">
                <select name="employee" class="form-select">
                    <option value="">{{ __('app.all_employees') }}</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" @selected(request('employee') == $emp->id)>{{ $emp->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            <div class="col-md-1">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-filter"></i></button>
            </div>
        </form>
    </div>
</div>

<div class="card overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th class="px-4">{{ __('app.task_name') }}</th>
                    <th>{{ __('app.employee') }}</th>
                    <th class="text-center">{{ __('app.priority') }}</th>
                    <th class="text-center">{{ __('app.status') }}</th>
                    <th>{{ __('app.deadline') }}</th>
                    <th class="text-end px-4">{{ __('app.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tasks as $task)
                <tr>
                    <td class="px-4 py-3">
                        <a href="{{ route('tasks.show', $task) }}" class="text-decoration-none text-body fw-bold d-block">{{ $task->title }}</a>
                        <small class="text-muted d-inline-block text-truncate" style="max-width: 250px;">{!! strip_tags($task->description) !!}</small>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 0.75rem; background: var(--input-bg);">
                                {{ substr($task->employee->name, 0, 1) }}
                            </div>
                            <span class="small fw-medium">{{ $task->employee->name }}</span>
                        </div>
                    </td>
                    <td class="text-center">
                        @php $p = ['low'=>'secondary', 'medium'=>'warning', 'high'=>'danger'][$task->priority]; @endphp
                        <span class="badge bg-{{ $p }} bg-opacity-10 text-{{ $p }} small text-uppercase">{{ \App\Models\Task::priorityLabel($task->priority) }}</span>
                    </td>
                    <td class="text-center">
                        @php 
                            $s = ['todo'=>'secondary', 'in_progress'=>'primary', 'testing'=>'info', 'done'=>'success', 'complete'=>'dark'][$task->status]; 
                        @endphp
                        <span class="badge bg-{{ $s }} bg-opacity-10 text-{{ $s }} small">{{ \App\Models\Task::statusLabel($task->status) }}</span>
                    </td>
                    <td>
                        <div class="small {{ $task->deadline && $task->deadline->isPast() && $task->status !== 'complete' ? 'text-danger fw-bold' : 'text-muted' }}">
                            {{ $task->deadline ? $task->deadline->format('d M, Y') : '-' }}
                        </div>
                    </td>
                    <td class="text-end px-4">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="background: var(--card-bg);">
                                <li><a class="dropdown-item text-body" href="{{ route('tasks.show', $task) }}"><i class="bi bi-eye me-2"></i> {{ __('app.view') }}</a></li>
                                @if(auth()->user()->isSuperadmin())
                                <li><a class="dropdown-item text-body" href="{{ route('tasks.edit', $task) }}"><i class="bi bi-pencil me-2"></i> {{ __('app.edit') }}</a></li>
                                <li><hr class="dropdown-divider opacity-50"></li>
                                <li>
                                    <form action="{{ route('tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('{{ __('app.delete_confirm') }}');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger"><i class="bi bi-trash me-2"></i> {{ __('app.delete') }}</button>
                                    </form>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <i class="bi bi-folder2-open text-muted" style="font-size: 3rem; opacity: 0.5;"></i>
                        <p class="text-muted mt-3">{{ __('app.no_data') }}</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $tasks->links('pagination::bootstrap-5') }}
</div>
@endsection
