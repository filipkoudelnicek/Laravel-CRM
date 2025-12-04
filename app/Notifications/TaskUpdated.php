<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class TaskUpdated extends Notification
{
    use Queueable;

    protected Task $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'task_updated',
            'title' => 'Úkol byl aktualizován',
            'message' => "Úkol: {$this->task->title}",
            'task_id' => $this->task->id,
        ];
    }
}

