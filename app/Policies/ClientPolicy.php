<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\User;

class ClientPolicy
{
    // Admin can do everything; member can view/edit only clients
    // that have at least one project assigned to the member.

    public function viewAny(User $user): bool
    {
        return true; // filtered in controller
    }

    public function view(User $user, Client $client): bool
    {
        if ($user->isAdmin()) return true;
        return $client->projects()->whereHas('users', fn ($q) => $q->where('user_id', $user->id))->exists();
    }

    public function create(User $user): bool
    {
        return true; // both admin and member can create clients
    }

    public function update(User $user, Client $client): bool
    {
        if ($user->isAdmin()) return true;
        return $client->projects()->whereHas('users', fn ($q) => $q->where('user_id', $user->id))->exists();
    }

    public function delete(User $user, Client $client): bool
    {
        if ($user->isAdmin()) return true;
        return $client->projects()->whereHas('users', fn ($q) => $q->where('user_id', $user->id))->exists();
    }
}
