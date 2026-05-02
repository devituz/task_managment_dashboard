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
            $this->sendRequest($employeeTelegramId, "🔔 *Sizga tegishli bildirishnoma:*\n\n" . $text);
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
        $priorityColor = ['low' => '🟢', 'medium' => '🟡', 'high' => '🔴'][$task->priority] ?? '⚪️';
        
        $text = "🆕 *YANGI TASK YARATILDI*\n\n";
        $text .= "📌 *Sarlavha:* {$task->title}\n";
        $text .= "👤 *Mas'ul xodim:* {$task->employee->name}\n";
        $text .= "{$priorityColor} *Muhimlik:* " . Task::priorityLabel($task->priority) . "\n";
        
        if ($task->deadline) {
            $text .= "⏰ *Muddat (Deadline):* " . $task->deadline->format('d-m-Y') . "\n";
        }
        
        $text .= "\n*Batafsil ma'lumot tizimda.*";

        $this->sendMessage($text, $task->employee->telegram_id);
    }

    public function statusChanged(Task $task, string $oldStatus): void
    {
        $text = "🔄 *TASK STATUSI O'ZGARDI*\n\n";
        $text .= "📌 *Sarlavha:* {$task->title}\n";
        $text .= "👤 *Mas'ul:* {$task->employee->name}\n\n";
        $text .= "📉 *Eski status:* " . Task::statusLabel($oldStatus) . "\n";
        $text .= "📈 *Yangi status:* " . Task::statusLabel($task->status) . "\n";

        $this->sendMessage($text, $task->employee->telegram_id);
    }

    public function taskCompleted(Task $task): void
    {
        $text = "✅ *TASK TO'LIQ YAKUNLANDI (COMPLETE)*\n\n";
        $text .= "📌 *Sarlavha:* {$task->title}\n";
        $text .= "👤 *Boshqargan xodim:* {$task->employee->name}\n";
        $text .= "🎉 *Tabriklaymiz, vazifa muvaffaqiyatli yopildi!*\n";

        $this->sendMessage($text, $task->employee->telegram_id);
    }

    public function taskDeleted(Task $task, string $deletedBy): void
    {
        $text = "🗑 *TASK O'CHIRILDI / BEKOR QILINDI*\n\n";
        $text .= "📌 *Sarlavha:* {$task->title}\n";
        $text .= "👤 *Mas'ul edi:* {$task->employee->name}\n";
        $text .= "👮‍♂️ *O'chirdi:* {$deletedBy}\n";

        $this->sendMessage($text, $task->employee->telegram_id);
    }

    public function commentAdded(Task $task, TaskComment $comment): void
    {
        $text = "💬 *TASKGA YANGI IZOH YOZILDI*\n\n";
        $text .= "📌 *Task:* {$task->title}\n";
        $text .= "✍️ *Yozuvchi:* {$comment->user->name}\n\n";
        $text .= "📝 *Izoh:* _{$comment->comment}_\n";

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
        $text = "📎 *TASKGA FAYL BIRIKTIRILDI*\n\n";
        $text .= "📌 *Task:* {$task->title}\n";
        $text .= "📁 *Fayl nomi:* {$fileName}\n";
        $text .= "👤 *Yukladi:* {$uploaderName}\n";

        $this->sendMessage($text, $task->employee->telegram_id);
    }
}