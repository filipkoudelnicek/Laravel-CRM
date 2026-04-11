<?php

namespace App\Providers;

use App\Models\Client;
use App\Models\Comment;
use App\Models\Invoice;
use App\Models\PasswordEntry;
use App\Models\Project;
use App\Models\SupportPlan;
use App\Models\Task;
use App\Models\TimeEntry;
use App\Policies\ClientPolicy;
use App\Policies\CommentPolicy;
use App\Policies\InvoicePolicy;
use App\Policies\PasswordEntryPolicy;
use App\Policies\ProjectPolicy;
use App\Policies\SupportPlanPolicy;
use App\Policies\TaskPolicy;
use App\Policies\TimeEntryPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Client::class        => ClientPolicy::class,
        Project::class       => ProjectPolicy::class,
        Task::class          => TaskPolicy::class,
        Comment::class       => CommentPolicy::class,
        PasswordEntry::class => PasswordEntryPolicy::class,
        TimeEntry::class     => TimeEntryPolicy::class,
        Invoice::class       => InvoicePolicy::class,
        SupportPlan::class   => SupportPlanPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        // Shorthand: admin bypasses all gates
        Gate::before(function ($user, $ability) {
            if ($user->isAdmin()) {
                return true;
            }
        });
    }
}

