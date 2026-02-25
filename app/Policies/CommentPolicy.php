<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
    private function canAccessTask(User $user, Comment $comment): bool
    {
        if ($user->isAdmin()) return true;
        return $comment->task->project()->whereHas('users', fn ($q) => $q->where('user_id', $user->id))->exists();
    }

    public function create(User $user): bool
    {
        return true; // project membership checked in controller
    }

    public function update(User $user, Comment $comment): bool
    {
        if ($user->isAdmin()) return true;
        return $comment->user_id === $user->id;
    }

    public function delete(User $user, Comment $comment): bool
    {
        if ($user->isAdmin()) return true;
        return $comment->user_id === $user->id;
    }
}
