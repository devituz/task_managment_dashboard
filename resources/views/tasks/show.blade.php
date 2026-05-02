@extends('layouts.app')

@section('title', __('app.task_info'))

@section('content')
<div class="mb-4">
    <a href="{{ route('tasks.index') }}" class="text-decoration-none text-muted"><i class="bi bi-arrow-left"></i> {{ __('app.back_to_tasks') }}</a>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h4 class="fw-bold mb-0">{{ $task->title }}</h4>
                    @if(auth()->user()->isSuperadmin())
                    <div>
                        <a href="{{ route('tasks.edit', $task) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i> {{ __('app.edit') }}</a>
                    </div>
                    @endif
                </div>

                <div class="mb-4">
                    @php
                        $pColor = ['low' => 'info', 'medium' => 'warning', 'high' => 'danger'][$task->priority] ?? 'secondary';
                        $sColor = ['todo' => 'secondary', 'in_progress' => 'primary', 'testing' => 'warning text-dark', 'done' => 'info text-dark', 'complete' => 'success'][$task->status] ?? 'secondary';
                    @endphp
                    <span class="badge bg-{{ $pColor }} me-2 mb-2"><i class="bi bi-flag"></i> {{ \App\Models\Task::priorityLabel($task->priority) }}</span>
                    <span class="badge bg-{{ $sColor }} mb-2"><i class="bi bi-circle-fill" style="font-size: 8px;"></i> {{ \App\Models\Task::statusLabel($task->status) }}</span>
                </div>

                <h6 class="fw-bold text-muted text-uppercase small mb-2">{{ __('app.description') }}</h6>
                <div class="p-3 rounded bg-body-tertiary ck-content">
                    {!! $task->description ?: __('app.no_data') !!}
                </div>
            </div>
        </div>

        <!-- Attached Files Section -->
        @if($task->files->count() > 0)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <h6 class="fw-bold text-muted text-uppercase small mb-3">{{ __('app.attachments') }} ({{ $task->files->count() }})</h6>
                <ul class="list-group list-group-flush border-top">
                    @foreach($task->files as $file)
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-file-earmark-text text-secondary fs-4 me-3"></i>
                            <div>
                                <a href="{{ Storage::url($file->file_path) }}" target="_blank" class="text-decoration-none fw-medium text-body d-block">
                                    {{ $file->file_name }}
                                </a>
                                <small class="text-muted">{{ number_format($file->file_size / 1024, 2) }} KB • Uploaded by {{ $file->uploader->name }}</small>
                            </div>
                        </div>
                        @if(auth()->user()->isSuperadmin() || $file->uploaded_by === auth()->id())
                        <form action="{{ route('tasks.files.destroy', $file) }}" method="POST" onsubmit="return confirm('{{ __('app.delete_confirm') }}');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger border-0"><i class="bi bi-trash"></i></button>
                        </form>
                        @endif
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

        <!-- File Upload Form (optional inside show) -->
        <div class="card border-0 shadow-sm mb-4 bg-body-tertiary">
            <div class="card-body p-3">
                <form action="{{ route('tasks.files.store', $task) }}" method="POST" enctype="multipart/form-data" class="d-flex align-items-center gap-2">
                    @csrf
                    <input type="file" name="files[]" class="form-control form-control-sm" multiple required>
                    <button type="submit" class="btn btn-sm btn-dark flex-shrink-0"><i class="bi bi-upload"></i> {{ __('app.upload') }}</button>
                </form>
            </div>
        </div>

        <!-- Comments Section -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h6 class="fw-bold text-muted text-uppercase small mb-4">{{ __('app.comments') }} ({{ $task->comments->count() }})</h6>
                
                @foreach($task->comments as $comment)
                <div class="d-flex mb-4">
                    <div class="flex-shrink-0 me-3">
                        <img src="{{ $comment->user->avatar_url }}" class="rounded-circle border shadow-sm" style="width: 40px; height: 40px; object-fit: cover;">
                    </div>
                    <div class="flex-grow-1 bg-body-tertiary p-3 rounded">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0 fw-bold fs-6">{{ $comment->user->name }}</h6>
                            <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                        </div>
                        <p class="mb-0 text-body" style="white-space: pre-line;">{{ $comment->comment }}</p>
                    </div>
                </div>
                @endforeach

                <form action="{{ route('tasks.comments.store', $task) }}" method="POST" class="mt-4">
                    @csrf
                    <div class="mb-3">
                        <textarea name="comment" class="form-control" rows="3" placeholder="{{ __('app.write_comment') }}" required></textarea>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-send"></i> {{ __('app.post_comment') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Status Update Card -->
        <div class="card border-0 shadow-sm border-top border-primary border-4 mb-4">
            <div class="card-body p-4">
                <h6 class="fw-bold text-muted text-uppercase small mb-3">{{ __('app.update_status') }}</h6>
                
                <form action="{{ route('tasks.status', $task) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="input-group">
                        <select name="status" class="form-select">
                            @foreach(\App\Models\Task::STATUSES as $status)
                                <option value="{{ $status }}" 
                                    @selected($task->status === $status) 
                                    @disabled(! $task->canMoveTo($status, auth()->user()) && $task->status !== $status)>
                                    {{ \App\Models\Task::statusLabel($status) }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary">{{ __('app.update') }}</button>
                    </div>
                    @if(!auth()->user()->isSuperadmin())
                        <div class="form-text mt-2"><i class="bi bi-info-circle"></i> {{ __('app.forward_only') }}</div>
                    @endif
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <h6 class="fw-bold text-muted text-uppercase small mb-3">{{ __('app.task_info') }}</h6>
                
                <div class="d-flex align-items-center mb-3">
                    <img src="{{ $task->employee->avatar_url }}" class="rounded-circle border shadow-sm me-3" style="width: 40px; height: 40px; object-fit: cover;">
                    <div>
                        <div class="small text-muted mb-1">{{ __('app.assigned_to') }}</div>
                        <div class="fw-medium">{{ $task->employee->name }}</div>
                    </div>
                </div>
                
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-body-tertiary text-secondary rounded-circle d-flex align-items-center justify-content-center me-3 border" style="width: 40px; height: 40px;">
                        <i class="bi bi-person"></i>
                    </div>
                    <div>
                        <div class="small text-muted mb-1">{{ __('app.created_by') }}</div>
                        <div class="fw-medium">{{ $task->creator?->name ?? 'System' }}</div>
                    </div>
                </div>

                <hr>

                <div class="mb-3">
                    <div class="small text-muted mb-1">{{ __('app.start_date') }}</div>
                    <div class="fw-medium">
                        <i class="bi bi-play-circle text-primary"></i> 
                        {{ $task->start_date ? $task->start_date->format('d F Y, H:i') : __('app.no_data') }}
                    </div>
                </div>

                <div class="mb-3">
                    <div class="small text-muted mb-1">{{ __('app.deadline_optional') }}</div>
                    <div class="fw-medium {{ $task->deadline && $task->deadline->isPast() && $task->status !== 'complete' ? 'text-danger' : '' }}">
                        <i class="bi bi-clock-history"></i> 
                        {{ $task->deadline ? $task->deadline->format('d F Y, H:i') : __('app.no_deadline') }}
                        @if($task->deadline && $task->deadline->isPast() && $task->status !== 'complete')
                            <span class="badge bg-danger ms-2">{{ __('app.overdue') }}</span>
                        @endif
                    </div>
                </div>

                <div class="mb-3">
                    <div class="small text-muted mb-1">{{ __('app.created_at') }}</div>
                    <div class="small">{{ $task->created_at->format('d M Y, H:i') }}</div>
                </div>

                <div>
                    <div class="small text-muted mb-1">{{ __('app.last_updated') }}</div>
                    <div class="small">{{ $task->updated_at->diffForHumans() }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection