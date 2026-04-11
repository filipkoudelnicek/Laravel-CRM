<?php

namespace App\Policies;

use App\Models\TimeEntry;
use App\Models\User;

class TimeEntryPolicy
{
    public function view(User $user, TimeEntry $timeEntry): bool
    {
        return $user->isAdmin()
            || $timeEntry->task->project->hasUser($user->id)
            || $timeEntry->created_by === $user->id;
    }

    public function create(User $user, $task): bool
    {
        return $user->isAdmin()
            || $task->project->hasUser($user->id);
    }

    public function update(User $user, TimeEntry $timeEntry): bool
    {
        return $user->isAdmin()
            || $timeEntry->created_by === $user->id;
    }

    public function delete(User $user, TimeEntry $timeEntry): bool
    {
        return $user->isAdmin()
            || $timeEntry->created_by === $user->id;
    }
}
