@csrf
<div class="space-y-4">
    <div>
        <label class="text-sm font-medium text-zinc-800" for="title">{{ __('app.title') }}</label>
        <input class="mt-1.5 w-full rounded-md border border-zinc-300 px-3 py-2.5 text-sm shadow-sm outline-none transition focus:border-zinc-900 focus:ring-2 focus:ring-zinc-900/10" id="title" name="title" value="{{ old('title', $task->title) }}" required>
    </div>
    <div>
        <label class="text-sm font-medium text-zinc-800" for="description">{{ __('app.description') }}</label>
        <textarea class="mt-1.5 min-h-32 w-full rounded-md border border-zinc-300 px-3 py-2.5 text-sm shadow-sm outline-none transition focus:border-zinc-900 focus:ring-2 focus:ring-zinc-900/10" id="description" name="description">{{ old('description', $task->description) }}</textarea>
    </div>
    <div class="grid gap-4 md:grid-cols-4">
        <div>
            <label class="text-sm font-medium text-zinc-800" for="user_id">{{ __('app.assigned_to') }}</label>
            <select class="mt-1.5 w-full rounded-md border border-zinc-300 px-3 py-2.5 text-sm shadow-sm outline-none transition focus:border-zinc-900 focus:ring-2 focus:ring-zinc-900/10" id="user_id" name="user_id" required>
                @foreach($employees as $employee)
                    <option value="{{ $employee->id }}" @selected((int) old('user_id', $task->user_id) === $employee->id)>{{ $employee->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-sm font-medium text-zinc-800" for="priority">{{ __('app.priority') }}</label>
            <select class="mt-1.5 w-full rounded-md border border-zinc-300 px-3 py-2.5 text-sm shadow-sm outline-none transition focus:border-zinc-900 focus:ring-2 focus:ring-zinc-900/10" id="priority" name="priority" required>
                @foreach(\App\Models\Task::PRIORITIES as $priority)
                    <option value="{{ $priority }}" @selected(old('priority', $task->priority) === $priority)>{{ __("app.priorities.{$priority}") }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-sm font-medium text-zinc-800" for="status">{{ __('app.status') }}</label>
            <select class="mt-1.5 w-full rounded-md border border-zinc-300 px-3 py-2.5 text-sm shadow-sm outline-none transition focus:border-zinc-900 focus:ring-2 focus:ring-zinc-900/10" id="status" name="status" required>
                @foreach(\App\Models\Task::STATUSES as $status)
                    <option value="{{ $status }}" @selected(old('status', $task->status) === $status)>{{ __("app.statuses.{$status}") }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-sm font-medium text-zinc-800" for="deadline">{{ __('app.deadline') }}</label>
            <input class="mt-1.5 w-full rounded-md border border-zinc-300 px-3 py-2.5 text-sm shadow-sm outline-none transition focus:border-zinc-900 focus:ring-2 focus:ring-zinc-900/10" id="deadline" name="deadline" type="date" value="{{ old('deadline', $task->deadline?->format('Y-m-d')) }}">
        </div>
    </div>
</div>
<div class="mt-6 flex gap-3">
    <button class="rounded-md bg-zinc-950 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-zinc-800">{{ __('app.save') }}</button>
    <a class="rounded-md border border-zinc-300 bg-white px-4 py-2.5 text-sm font-semibold text-zinc-700 transition hover:bg-zinc-50" href="{{ route('tasks.index') }}">{{ __('app.cancel') }}</a>
</div>
