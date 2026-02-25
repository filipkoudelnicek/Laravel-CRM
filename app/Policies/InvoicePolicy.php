<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;

class InvoicePolicy
{
    public function viewAny(User $user): bool
    {
        return true; // filtered in controller for non-admins
    }

    public function view(User $user, Invoice $invoice): bool
    {
        if ($user->isAdmin()) return true;
        // member can see invoices for projects they're assigned to
        if ($invoice->project_id) {
            return $invoice->project->hasUser($user->id);
        }
        return $invoice->client->projects()
            ->whereHas('users', fn ($q) => $q->where('user_id', $user->id))
            ->exists();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Invoice $invoice): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Invoice $invoice): bool
    {
        return $user->isAdmin();
    }
}
