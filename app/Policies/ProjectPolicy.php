<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // filtered in controller
    }

    public function view(User $user, Project $project): bool
    {
        if ($user->isAdmin()) return true;
        return $project->hasUser($user->id);
    }

    public function create(User $user): bool
    {
        return true; // both admin and member can create projects
    }

    public function update(User $user, Project $project): bool
    {
        if ($user->isAdmin()) return true;
        return $project->hasUser($user->id);
    }

    public function delete(User $user, Project $project): bool
    {
        if ($user->isAdmin()) return true;
        return $project->hasUser($user->id);
    }

    public function manageMembers(User $user, Project $project): bool
    {
        return $user->isAdmin();
    }
}
