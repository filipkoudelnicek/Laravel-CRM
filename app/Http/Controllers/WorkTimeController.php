<?php

namespace App\Http\Controllers;

use App\Models\WorkTimeEntry;
use App\Models\TimeEntry;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class WorkTimeController extends Controller
{
    public function getActive(): JsonResponse
    {
        $active = WorkTimeEntry::where('user_id', auth()->id())
            ->whereIn('status', ['running', 'paused'])
            ->first();

        return response()->json($active);
    }

    public function start(): JsonResponse
    {
        // Zkontrolovat, zda už existuje aktivní session
        $active = WorkTimeEntry::where('user_id', auth()->id())
            ->whereIn('status', ['running', 'paused'])
            ->first();

        if ($active) {
            return response()->json(['error' => 'Active session already exists'], 400);
        }

        $workTimeEntry = WorkTimeEntry::create([
            'user_id' => auth()->id(),
            'start_time' => now(),
            'status' => 'running',
        ]);

        return response()->json($workTimeEntry);
    }

    public function pause(WorkTimeEntry $workTimeEntry): JsonResponse
    {
        if ($workTimeEntry->user_id !== auth()->id()) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $workTimeEntry->update([
            'status' => 'paused',
        ]);

        return response()->json($workTimeEntry);
    }

    public function resume(Request $request, WorkTimeEntry $workTimeEntry): JsonResponse
    {
        if ($workTimeEntry->user_id !== auth()->id()) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $pausedSeconds = $request->input('paused_seconds', 0);
        
        $workTimeEntry->update([
            'status' => 'running',
            'paused_duration' => $workTimeEntry->paused_duration + $pausedSeconds,
        ]);

        return response()->json($workTimeEntry);
    }

    public function stop(Request $request, WorkTimeEntry $workTimeEntry): JsonResponse
    {
        if ($workTimeEntry->user_id !== auth()->id()) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $now = now();
        $startTime = $workTimeEntry->start_time;
        $totalSeconds = $now->diffInSeconds($startTime);
        $workSeconds = $totalSeconds - $workTimeEntry->paused_duration;
        $durationMinutes = max(1, ceil($workSeconds / 60));

        $workTimeEntry->update([
            'status' => 'stopped',
            'end_time' => $now,
        ]);

        // Vytvořit TimeEntry
        TimeEntry::create([
            'user_id' => auth()->id(),
            'description' => $request->input('description'),
            'duration' => $durationMinutes,
            'date' => now(),
            'start_time' => $startTime,
            'end_time' => $now,
        ]);

        return response()->json($workTimeEntry);
    }

    public function stopOnLogout(): JsonResponse
    {
        $activeSessions = WorkTimeEntry::where('user_id', auth()->id())
            ->whereIn('status', ['running', 'paused'])
            ->get();

        foreach ($activeSessions as $session) {
            $session->update([
                'status' => 'stopped',
                'end_time' => now(),
            ]);
        }

        return response()->json(['success' => true, 'stopped' => $activeSessions->count()]);
    }
}

