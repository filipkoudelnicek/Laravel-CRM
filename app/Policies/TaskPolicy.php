<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    private function canAccessProject(User $user, Task $task): bool
    {
        if ($user->isAdmin()) return true;
        return $task->project()->whereHas('users', fn ($q) => $q->where('user_id', $user->id))->exists();
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Task $task): bool
    {
        return $this->canAccessProject($user, $task);
    }

    public function create(User $user): bool
    {
        return true; // access to specific project checked in controller
    }

    public function update(User $user, Task $task): bool
    {
        return $this->canAccessProject($user, $task);
    }

    public function delete(User $user, Task $task): bool
    {
        return $this->canAccessProject($user, $task);
    }
}
