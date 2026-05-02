<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\Task;
use App\Models\TaskComment;
use App\Models\TaskFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramNotifier
{
    private ?string $token;
    private ?string $channelId;

    public function __construct()
    {
        $this->token = Setting::getValue('telegram_bot_token');
        $this->channelId = Setting::getValue('telegram_channel_id');
    }

    /**
     * Asosiy xabar yuborish mantiqi (Channel va Xodimga alohida)
     */
    private function sendMessage(string $text, ?string $employeeTelegramId = null): void
    {
        if (! $this->token) {
            return;
        }

        // 1. Umumiy kanal/guruhga yuborish
        if ($this->channelId) {
            $this->sendRequest($this->channelId, $text);
        }

        // 2. Agar xodimning shaxsiy Telegram ID si bo'lsa va u guruh ID sidan farq qilsa, unga ham alohida yuborish
        if ($employeeTelegramId && $employeeTelegramId !== $this->channelId) {
            $this->sendRequest($employeeTelegramId, "🔔 *Personal Notification:*\n\n" . $text);
        }
    }

    private function sendRequest(string $chatId, string $text): void
    {
        try {
            Http::post("https://api.telegram.org/bot{$this->token}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'Markdown',
                'disable_web_page_preview' => true,
            ]);
        } catch (\Exception $e) {
            Log::error('Telegram notification failed: ' . $e->getMessage());
        }
    }

    public function taskCreated(Task $task): void
    {
        $priorityLabel = Task::priorityLabel($task->priority);
        $description = strip_tags($task->description);
        $shortDescription = mb_strlen($description) > 100 ? mb_substr($description, 0, 100) . '...' : $description;
        $dashboardUrl = config('app.url') . "/tasks/" . $task->id;

        $text = "🆕 NEW TASK\n\n";
        $text .= "*Title:* {$task->title}\n";
        $text .= "*Descriptions:* {$shortDescription}\n";
        $text .= "*Employee:* {$task->employee->name}\n";
        $text .= "*Priority:* {$priorityLabel}\n\n";
        $text .= "🔗 *Dashboard:* [View]({$dashboardUrl})";

        $this->sendMessage($text, $task->employee->telegram_id);
    }

    public function statusChanged(Task $task, string $oldStatus): void
    {
        $oldStatusLabel = Task::statusLabel($oldStatus);
        $newStatusLabel = Task::statusLabel($task->status);

        $text = "STATUS UPDATED\n\n";
        $text .= "Title: {$task->title}\n";
        $text .= "Employee: {$task->employee->name}\n\n";
        $text .= "{$oldStatusLabel} → {$newStatusLabel}";

        $this->sendMessage($text, $task->employee->telegram_id);
    }

    public function taskCompleted(Task $task): void
    {
        $text = "TASK COMPLETED\n\n";
        $text .= "Title: {$task->title}\n";
        $text .= "Employee: {$task->employee->name}\n";
        $text .= "The task has been successfully closed.";

        $this->sendMessage($text, $task->employee->telegram_id);
    }

    public function taskDeleted(Task $task, string $deletedBy): void
    {
        $text = "TASK DELETED / CANCELLED\n\n";
        $text .= "Title: {$task->title}\n";
        $text .= "Was assigned to: {$task->employee->name}\n";
        $text .= "Deleted by: {$deletedBy}";

        $this->sendMessage($text, $task->employee->telegram_id);
    }

    public function commentAdded(Task $task, TaskComment $comment): void
    {
        $text = "NEW COMMENT ADDED\n\n";
        $text .= "Task: {$task->title}\n";
        $text .= "Author: {$comment->user->name}\n\n";
        $text .= "Comment: {$comment->comment}";

        // Agar izohni boshqa odam yozgan bo'lsa (masalan admin), xodimga xabar ketishi kerak
        $targetTelegramId = $task->employee->telegram_id;
        if ($comment->user_id === $task->user_id) {
            // Agar izohni xodimning o'zi yozgan bo'lsa, xodimga takror yubormaslik (yoki adminga yuborish)
            $targetTelegramId = null; 
        }

        $this->sendMessage($text, $targetTelegramId);
    }

    public function fileUploaded(Task $task, string $fileName, string $uploaderName): void
    {
        $text = "FILE ATTACHED TO TASK\n\n";
        $text .= "Task: {$task->title}\n";
        $text .= "File name: {$fileName}\n";
        $text .= "Uploaded by: {$uploaderName}";

        $this->sendMessage($text, $task->employee->telegram_id);
    }
}