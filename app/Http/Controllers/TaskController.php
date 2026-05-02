<?php

namespace App\Http\Controllers;

use App\Events\TaskEvent;
use App\Models\Task;
use App\Models\TaskFile;
use App\Models\User;
use App\Services\TelegramNotifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Task::with(['employee', 'files', 'comments'])->latest();

        if (! $request->user()->isSuperadmin()) {
            $query->where('user_id', $request->user()->id);
        }

        $query->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')->toString()));
        $query->when($request->filled('priority'), fn ($query) => $query->where('priority', $request->string('priority')->toString()));
        $query->when($request->filled('employee') && $request->user()->isSuperadmin(), fn ($query) => $query->where('user_id', $request->integer('employee')));
        $query->when($request->filled('search'), function ($query) use ($request) {
            $search = '%'.$request->string('search')->toString().'%';
            $query->where(fn ($query) => $query->where('title', 'like', $search)->orWhere('description', 'like', $search));
        });

        return view('tasks.index', [
            'tasks' => $query->paginate(12)->withQueryString(),
            'employees' => User::where('role', User::ROLE_EMPLOYER)->orderBy('name')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('tasks.create', [
            'task' => new Task(['priority' => 'medium', 'status' => 'todo']),
            'employees' => User::where('role', User::ROLE_EMPLOYER)->orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, TelegramNotifier $notifier): RedirectResponse
    {
        $data = $this->validated($request);
        $data['created_by'] = $request->user()->id;

        $task = Task::create($data);
        $this->handleFilesUpload($request, $task, $notifier);

        $task->load('employee');
        $notifier->taskCreated($task);

        event(new TaskEvent("Yangi vazifa biriktirildi: {$task->title}", 'success'));

        return redirect()->route('tasks.index')->with('success', __('app.task_created') ?? 'Task created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Task $task): View
    {
        $this->authorizeTaskView($request, $task);
        $task->load(['employee', 'files.uploader', 'comments.user', 'creator']);

        return view('tasks.show', compact('task'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task): View
    {
        return view('tasks.edit', [
            'task' => $task,
            'employees' => User::where('role', User::ROLE_EMPLOYER)->orderBy('name')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task, TelegramNotifier $notifier): RedirectResponse
    {
        $oldStatus = $task->status;

        $task->update($this->validated($request));
        $this->handleFilesUpload($request, $task, $notifier);
        $task->load('employee');

        if ($oldStatus !== $task->status) {
            $notifier->statusChanged($task, $oldStatus);
        }

        return redirect()->route('tasks.index')->with('success', __('app.task_updated') ?? 'Task updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Task $task, TelegramNotifier $notifier): RedirectResponse
    {
        $taskName = $task->title;
        $deletedBy = $request->user()->name;
        $task->load('employee');

        foreach ($task->files as $file) {
            Storage::disk('public')->delete($file->file_path);
        }
        $task->delete();

        $notifier->taskDeleted($task, $deletedBy);

        return redirect()->route('tasks.index')->with('success', __('app.task_deleted') ?? 'Task deleted successfully.');
    }

    public function updateStatus(Request $request, Task $task, TelegramNotifier $notifier): RedirectResponse
    {
        $this->authorizeTaskView($request, $task);

        $data = $request->validate([
            'status' => ['required', Rule::in(Task::STATUSES)],
        ]);

        abort_unless($task->canMoveTo($data['status'], $request->user()), 422, __('app.invalid_transition') ?? 'Invalid status transition.');

        $oldStatus = $task->status;
        $task->update(['status' => $data['status']]);
        $task->load('employee');

        if ($task->status === 'complete') {
            $notifier->taskCompleted($task);
            event(new TaskEvent("Vazifa yakunlandi: {$task->title}", 'success'));
        } else {
            $notifier->statusChanged($task, $oldStatus);
            event(new TaskEvent("Status yangilandi: {$task->title} -> " . Task::statusLabel($task->status), 'info'));
        }

        return back()->with('success', __('app.status_updated') ?? 'Status updated successfully.');
    }

    public function uploadFiles(Request $request, Task $task, TelegramNotifier $notifier): RedirectResponse
    {
        $this->authorizeTaskView($request, $task);
        $this->handleFilesUpload($request, $task, $notifier);
        
        return back()->with('success', 'Files uploaded successfully.');
    }

    public function deleteFile(Request $request, TaskFile $taskFile): RedirectResponse
    {
        abort_unless($request->user()->isSuperadmin() || $taskFile->uploaded_by === $request->user()->id, 403);
        
        Storage::disk('public')->delete($taskFile->file_path);
        $taskFile->delete();

        return back()->with('success', 'File deleted successfully.');
    }

    public function storeComment(Request $request, Task $task, TelegramNotifier $notifier): RedirectResponse
    {
        $this->authorizeTaskView($request, $task);

        $data = $request->validate([
            'comment' => ['required', 'string'],
        ]);

        $comment = $task->comments()->create([
            'user_id' => $request->user()->id,
            'comment' => $data['comment'],
        ]);

        $comment->load('user');
        $task->load('employee');
        $notifier->commentAdded($task, $comment);

        return back()->with('success', 'Comment added successfully.');
    }

    private function handleFilesUpload(Request $request, Task $task, ?TelegramNotifier $notifier = null): void
    {
        $request->validate([
            'files.*' => ['nullable', 'file', 'max:10240', 'mimes:jpg,jpeg,png,webp,pdf,doc,docx,xls,xlsx,zip'],
            'files' => ['max:10']
        ]);

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('tasks', 'public');
                $task->files()->create([
                    'uploaded_by' => $request->user()->id,
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ]);

                if ($notifier) {
                    $task->load('employee');
                    $notifier->fileUploaded($task, $file->getClientOriginalName(), $request->user()->name);
                }
            }
        }
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'description' => ['required', 'string'],
            'user_id' => ['required', Rule::exists('users', 'id')->where('role', User::ROLE_EMPLOYER)],
            'priority' => ['required', Rule::in(Task::PRIORITIES)],
            'status' => ['required', Rule::in(Task::STATUSES)],
            'deadline' => ['nullable', 'date'],
        ]);
    }

    private function authorizeTaskView(Request $request, Task $task): void
    {
        abort_unless($request->user()->isSuperadmin() || $task->user_id === $request->user()->id, 403);
    }
}
