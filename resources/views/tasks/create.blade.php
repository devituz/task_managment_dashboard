@extends('layouts.app')

@section('title', isset($task->id) ? __('app.edit_task') : __('app.create_task'))

@section('content')
<div class="mb-4">
    <a href="{{ route('tasks.index') }}" class="text-decoration-none text-muted"><i class="bi bi-arrow-left"></i> {{ __('app.back_to_tasks') }}</a>
</div>

<div class="card border-0 shadow-sm" style="max-width: 800px;">
    <div class="card-body p-4">
        <h5 class="card-title fw-bold mb-4">{{ isset($task->id) ? __('app.edit_task') . ': ' . $task->title : __('app.create_task') }}</h5>

        <form action="{{ isset($task->id) ? route('tasks.update', $task) : route('tasks.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if(isset($task->id))
                @method('PUT')
            @endif

            <div class="mb-3">
                <label class="form-label fw-medium">{{ __('app.title') }}</label>
                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $task->title ?? '') }}" required>
                @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-medium">{{ __('app.description') }}</label>
                <textarea id="task-description" name="description" class="form-control @error('description') is-invalid @enderror" rows="8">{{ old('description', $task->description ?? '') }}</textarea>
                @error('description') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="mb-4">
                <label class="form-label fw-medium">{{ __('app.attachments') }} ({{ __('app.max_files') }})</label>
                <input type="file" name="files[]" class="form-control @error('files.*') is-invalid @enderror" multiple accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx,.xls,.xlsx,.zip">
                @error('files.*') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                @error('files') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-medium">{{ __('app.assign_to') }}</label>
                    <select name="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                        <option value="">{{ __('app.select_employee') }}</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" @selected(old('user_id', $task->user_id ?? '') == $emp->id)>{{ $emp->name }}</option>
                        @endforeach
                    </select>
                    @error('user_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-medium">{{ __('app.priority') }}</label>
                    <select name="priority" class="form-select @error('priority') is-invalid @enderror" required>
                        @foreach(\App\Models\Task::PRIORITIES as $priority)
                            <option value="{{ $priority }}" @selected(old('priority', $task->priority ?? 'medium') === $priority)>{{ \App\Models\Task::priorityLabel($priority) }}</option>
                        @endforeach
                    </select>
                    @error('priority') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-medium">{{ __('app.status') }}</label>
                    <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                        @foreach(\App\Models\Task::STATUSES as $status)
                            <option value="{{ $status }}" @selected(old('status', $task->status ?? 'todo') === $status)>{{ \App\Models\Task::statusLabel($status) }}</option>
                        @endforeach
                    </select>
                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-medium">{{ __('app.deadline_optional') }}</label>
                    <input type="date" name="deadline" class="form-control @error('deadline') is-invalid @enderror" value="{{ old('deadline', isset($task->deadline) ? \Carbon\Carbon::parse($task->deadline)->format('Y-m-d') : '') }}">
                    @error('deadline') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <hr class="text-muted">

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('tasks.index') }}" class="btn btn-light border">{{ __('app.cancel') }}</a>
                <button type="submit" class="btn btn-dark">{{ isset($task->id) ? __('app.save_changes') : __('app.create_task') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
    ClassicEditor
        .create(document.querySelector('#task-description'))
        .catch(error => {
            console.error(error);
        });
</script>
<style>
    .ck-editor__editable { min-height: 200px; }
</style>
@endsection