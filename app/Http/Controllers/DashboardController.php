<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Models\Client;
use App\Models\TimeEntry;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        // Statistiky úkolů
        $tasksQuery = Task::query();
        if (!$user->isAdmin()) {
            $tasksQuery->where(function($q) use ($user) {
                $q->where('created_by_id', $user->id)
                  ->orWhere('assigned_to_id', $user->id);
            });
        }

        $totalTasks = $tasksQuery->count();
        $tasksByStatus = $tasksQuery->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        // Statistiky projektů a klientů
        $totalProjects = Project::count();
        $totalClients = Client::count();

        // Time tracking statistiky
        $timeEntriesQuery = TimeEntry::query();
        if (!$user->isAdmin()) {
            $timeEntriesQuery->where('user_id', $user->id);
        }

        $totalTrackedTime = $timeEntriesQuery->sum('duration');
        $thisMonthTime = $timeEntriesQuery->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum('duration');

        // Overdue úkoly
        $overdueTasks = Task::where('due_date', '<', now())
            ->where('status', '!=', 'done')
            ->with(['project.client', 'assignedTo'])
            ->latest('due_date')
            ->take(5)
            ->get();

        return view('dashboard.index', compact(
            'totalTasks',
            'tasksByStatus',
            'totalProjects',
            'totalClients',
            'totalTrackedTime',
            'thisMonthTime',
            'overdueTasks'
        ));
    }
}

