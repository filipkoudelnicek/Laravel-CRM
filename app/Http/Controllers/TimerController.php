<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TimeEntry;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TimerController extends Controller
{
    public function start(Request $request, Task $task)
    {
        $this->authorize('create', [TimeEntry::class, $task]);

        DB::transaction(function () use ($request, $task) {
            // Lock the user, including when no running entry exists yet.
            User::whereKey($request->user()->id)->lockForUpdate()->firstOrFail();
            if (TimeEntry::where('created_by', $request->user()->id)->whereNull('ended_at')->exists()) {
                throw ValidationException::withMessages(['timer' => 'Nejdříve zastavte svůj běžící časovač.']);
            }
            $task->timeEntries()->create(['started_at' => now(), 'created_by' => $request->user()->id]);
        });

        return back()->with('success', 'Měření času spuštěno.');
    }

    public function stop(Request $request, TimeEntry $timeEntry)
    {
        // A colleague or admin must never stop another person's running timer.
        abort_unless($timeEntry->created_by === $request->user()->id, 403);
        $data = $request->validate(['notes' => 'nullable|string|max:500']);

        DB::transaction(function () use ($timeEntry, $data) {
            $entry = TimeEntry::whereKey($timeEntry->id)->lockForUpdate()->firstOrFail();
            if ($entry->ended_at === null) {
                $entry->update([
                    'ended_at' => now()->max($entry->started_at),
                    'notes' => $data['notes'] ?? $entry->notes,
                ]);
            }
        });

        return back()->with('success', 'Odpracovaný čas uložen.');
    }
}
