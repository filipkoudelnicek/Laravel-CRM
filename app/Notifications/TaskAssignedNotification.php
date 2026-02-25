<?php

namespace App\Notifications;

use App\Models\Task;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TaskAssignedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Task $task,
        public User $assignedBy,
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type'       => 'task_assigned',
            'task_id'    => $this->task->id,
            'task_title' => $this->task->title,
            'project_id' => $this->task->project_id,
            'by_user'    => $this->assignedBy->name,
            'message'    => $this->assignedBy->name . ' přiřadil(a) vás k úkolu "' . $this->task->title . '".',
        ];
    }
}
