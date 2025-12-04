<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class CommentMention extends Notification
{
    use Queueable;

    protected Comment $comment;

    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'comment_mention',
            'title' => 'Byli jste zmíněni v komentáři',
            'message' => ($this->comment->user ? $this->comment->user->name : 'Neznámý uživatel') . " vás zmínil v komentáři",
            'task_id' => $this->comment->task_id,
        ];
    }
}

