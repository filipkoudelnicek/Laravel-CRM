<?php

namespace App\Policies;

use App\Models\SupportPlan;
use App\Models\User;

class SupportPlanPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, SupportPlan $plan): bool
    {
        if ($user->isAdmin()) return true;
        return $plan->client->projects()
            ->whereHas('users', fn ($q) => $q->where('user_id', $user->id))
            ->exists();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, SupportPlan $plan): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, SupportPlan $plan): bool
    {
        return $user->isAdmin();
    }
}
