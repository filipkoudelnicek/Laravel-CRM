<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class TaskAssigned extends Notification
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
            'type' => 'task_assigned',
            'title' => 'Byl jste přiřazen k úkolu',
            'message' => "Úkol: {$this->task->title}",
            'task_id' => $this->task->id,
        ];
    }
}

