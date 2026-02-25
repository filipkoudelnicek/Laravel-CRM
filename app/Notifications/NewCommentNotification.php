<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewCommentNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Comment $comment,
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $task = $this->comment->task;

        return [
            'type'       => 'new_comment',
            'task_id'    => $task->id,
            'task_title' => $task->title,
            'comment_id' => $this->comment->id,
            'by_user'    => $this->comment->user->name,
            'excerpt'    => \Str::limit(strip_tags($this->comment->body), 80),
            'message'    => $this->comment->user->name . ' komentoval(a) úkol "' . $task->title . '".',
        ];
    }
}
