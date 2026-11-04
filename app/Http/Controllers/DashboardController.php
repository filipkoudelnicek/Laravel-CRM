<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectNote;
use App\Models\SupportPlan;
use App\Models\Task;
use App\Models\TimeEntry;
use App\Support\VisibilityScope;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $taskQuery = Task::whereHas('project', fn ($q) => VisibilityScope::projects($q, $user));
        $myQuery = (clone $taskQuery)->whereHas('assignees', fn ($q) => $q->where('users.id', $user->id))
            ->where('status', '!=', 'done');
        $myTaskCount = (clone $myQuery)->count();
        $overdueCount = (clone $myQuery)->whereDate('due_at', '<', now(config('app.display_timezone'))->toDateString())->count();
        $myTasks = (clone $myQuery)->with(['project.client', 'assignees'])
            ->orderByRaw('due_at IS NULL')->orderBy('due_at')->orderByDesc('created_at')->limit(8)->get();

        $activeProjects = VisibilityScope::projects(Project::query(), $user)->where('status', 'active')->count();
        $serviceQuery = VisibilityScope::supportPlans(SupportPlan::query(), $user)->expiringSoon(30);
        $expiringCount = (clone $serviceQuery)->count();
        $expiringServices = $serviceQuery->with('client')->orderBy('period_to')->limit(5)->get();
        $recentNotes = ProjectNote::with(['user', 'project.client'])
            ->whereHas('project', fn ($q) => VisibilityScope::projects($q, $user))->latest()->limit(5)->get();

        $weekStart = now(config('app.display_timezone'))->startOfWeek()->utc();
        $weekMinutes = TimeEntry::where('created_by', $user->id)->whereNotNull('ended_at')
            ->where('ended_at', '>=', $weekStart)->where('started_at', '<=', now())->get()
            ->sum(fn ($e) => (int) max(0, $e->started_at->max($weekStart)->diffInMinutes($e->ended_at->min(now()), false)));

        return view('dashboard', compact('myTasks', 'myTaskCount', 'overdueCount', 'activeProjects',
            'expiringCount', 'expiringServices', 'recentNotes', 'weekMinutes'));
    }
}
