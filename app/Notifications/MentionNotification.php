<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MentionNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly Comment $comment
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'comment_id' => $this->comment->id,
            'task_id'    => $this->comment->task_id,
            'task_title' => $this->comment->task->title ?? '',
            'by_user'    => $this->comment->user->name ?? '',
            'excerpt'    => mb_substr($this->comment->body, 0, 100),
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
