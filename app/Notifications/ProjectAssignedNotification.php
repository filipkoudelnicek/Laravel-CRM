<?php

namespace App\Notifications;

use App\Models\Project;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ProjectAssignedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Project $project,
        public User $assignedBy,
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type'         => 'project_assigned',
            'project_id'   => $this->project->id,
            'project_name' => $this->project->name,
            'by_user'      => $this->assignedBy->name,
            'message'      => $this->assignedBy->name . ' přiřadil(a) vás k projektu "' . $this->project->name . '".',
        ];
    }
}
