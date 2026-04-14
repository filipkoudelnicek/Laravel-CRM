<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class VisibilityScope
{
    public static function projects(Builder $query, User $user): Builder
    {
        if (!$user->isAdmin()) {
            $query->whereHas('users', fn ($q) => $q->where('user_id', $user->id));
        }

        return $query;
    }

    public static function clients(Builder $query, User $user): Builder
    {
        if (!$user->isAdmin()) {
            $query->whereHas('projects.users', fn ($q) => $q->where('user_id', $user->id));
        }

        return $query;
    }

    public static function invoices(Builder $query, User $user): Builder
    {
        if (!$user->isAdmin()) {
            $query->where(function ($q) use ($user) {
                $q->whereHas('project.users', fn ($sub) => $sub->where('user_id', $user->id))
                  ->orWhereHas('client.projects.users', fn ($sub) => $sub->where('user_id', $user->id));
            });
        }

        return $query;
    }

    public static function supportPlans(Builder $query, User $user): Builder
    {
        if (!$user->isAdmin()) {
            $query->whereHas('client.projects.users', fn ($q) => $q->where('user_id', $user->id));
        }

        return $query;
    }
}
