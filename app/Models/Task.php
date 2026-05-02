<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'user_id',
        'created_by',
        'priority',
        'status',
        'deadline',
    ];

    public const PRIORITIES = ['low', 'medium', 'high'];

    public const STATUSES = ['todo', 'in_progress', 'testing', 'done', 'complete'];

    protected function casts(): array
    {
        return [
            'deadline' => 'date',
        ];
    }

    public static function statusLabel(string $status): string
    {
        return match ($status) {
            'todo' => 'Todo',
            'in_progress' => 'In Progress',
            'testing' => 'Testing',
            'done' => 'Done',
            'complete' => 'Complete',
            default => ucfirst(str_replace('_', ' ', $status)),
        };
    }

    public static function priorityLabel(string $priority): string
    {
        return match ($priority) {
            'low' => 'Low',
            'medium' => 'Medium',
            'high' => 'High',
            default => ucfirst($priority),
        };
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function files(): HasMany
    {
        return $this->hasMany(TaskFile::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TaskComment::class);
    }

    public function canMoveTo(string $status, User $user): bool
    {
        if (! in_array($status, self::STATUSES, true)) {
            return false;
        }

        if ($user->isSuperadmin()) {
            return true; // Superadmin istalgan statusga o'tkaza oladi (Complete ni ham o'z ichiga oladi)
        }

        // Employer faqatgina o'ziga biriktirilgan taskni update qila oladi
        if ($this->user_id !== $user->id) {
            return false;
        }

        // Employer faqatgina keyingi logik qadamlarga o'tkaza oladi, 'complete' qilolmaydi.
        $transitions = [
            'todo' => ['in_progress'],
            'in_progress' => ['testing'],
            'testing' => ['done'],
            'done' => [],
            'complete' => [],
        ];

        return in_array($status, $transitions[$this->status] ?? [], true);
    }
}
