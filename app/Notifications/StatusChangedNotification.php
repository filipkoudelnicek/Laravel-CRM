<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notification;

class StatusChangedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Model $model,         // Project or Task
        public string $oldStatus,
        public string $newStatus,
        public User $changedBy,
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $isProject = $this->model instanceof \App\Models\Project;
        $type      = $isProject ? 'project' : 'task';
        $name      = $isProject ? $this->model->name : $this->model->title;

        return [
            'type'       => $type . '_status_changed',
            'model_type' => $type,
            'model_id'   => $this->model->id,
            'name'       => $name,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'by_user'    => $this->changedBy->name,
            'message'    => $this->changedBy->name . ' změnil(a) stav ' . ($isProject ? 'projektu' : 'úkolu') . ' "' . $name . '" na "' . str_replace('_', ' ', $this->newStatus) . '".',
        ];
    }
}
