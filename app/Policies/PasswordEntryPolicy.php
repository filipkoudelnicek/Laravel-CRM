<?php

namespace App\Policies;

use App\Models\PasswordEntry;
use App\Models\User;

class PasswordEntryPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, PasswordEntry $entry): bool
    {
        if ($user->isAdmin()) return true;
        // member can view if they have a project assigned that matches entry's project
        if ($entry->project_id) {
            return $entry->project()->whereHas('users', fn ($q) => $q->where('user_id', $user->id))->exists();
        }
        // or if they created it
        return $entry->created_by === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, PasswordEntry $entry): bool
    {
        if ($user->isAdmin()) return true;
        return $entry->created_by === $user->id;
    }

    public function delete(User $user, PasswordEntry $entry): bool
    {
        if ($user->isAdmin()) return true;
        return $entry->created_by === $user->id;
    }

    public function reveal(User $user, PasswordEntry $entry): bool
    {
        return $this->view($user, $entry);
    }
}
