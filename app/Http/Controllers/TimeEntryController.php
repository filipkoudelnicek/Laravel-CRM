<?php

namespace App\Http\Controllers;

use App\Models\TimeEntry;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TimeEntryController extends Controller
{
    public function index(Request $request): View
    {
        $query = TimeEntry::with(['user', 'task.project.client']);

        // Filtrování podle uživatele (pro ADMIN)
        if (!auth()->user()->isAdmin() || $request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id', auth()->id()));
        }

        // Filtrování podle měsíce a roku
        if ($request->filled('month') && $request->filled('year')) {
            $startDate = now()->setYear($request->year)->setMonth($request->month)->startOfMonth();
            $endDate = now()->setYear($request->year)->setMonth($request->month)->endOfMonth();
            
            $query->whereBetween('date', [$startDate, $endDate]);
        }

        $timeEntries = $query->latest('date')->get();
        $groupedEntries = $timeEntries->groupBy(function($entry) {
            return $entry->date->format('Y-m-d');
        });

        $totalTime = $timeEntries->sum('duration');

        return view('time-tracking.index', compact('timeEntries', 'groupedEntries', 'totalTime'));
    }

    public function reports(Request $request): View
    {
        $query = TimeEntry::with(['user', 'task.project.client'])
            ->whereNotNull('task_id'); // Pouze časové záznamy s úkolem

        if (!auth()->user()->isAdmin() || $request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id', auth()->id()));
        }

        if ($request->filled('month') && $request->filled('year')) {
            $startDate = now()->setYear($request->year)->setMonth($request->month)->startOfMonth();
            $endDate = now()->setYear($request->year)->setMonth($request->month)->endOfMonth();
            
            $query->whereBetween('date', [$startDate, $endDate]);
        }

        $timeEntries = $query->latest('date')->get();
        $groupedEntries = $timeEntries->groupBy(function($entry) {
            return $entry->date->format('Y-m-d');
        });

        $totalTime = $timeEntries->sum('duration');

        return view('reports.index', compact('timeEntries', 'groupedEntries', 'totalTime'));
    }
}

