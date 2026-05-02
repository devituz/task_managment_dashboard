<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public string $message,
        public string $type = 'info',
        public ?int $userId = null
    ) {}

    public function broadcastOn(): array
    {
        // Agar userId bo'lsa, xodimning shaxsiy kanaliga, bo'lmasa umumiy kanalga
        return [
            new Channel('tasks-channel'),
            $this->userId ? new PrivateChannel('user.' . $this->userId) : null,
        ];
    }

    public function broadcastAs(): string
    {
        return 'task.notification';
    }
}