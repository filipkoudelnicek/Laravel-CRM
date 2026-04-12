<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TimeEntry;
use Illuminate\Http\Request;

class TimeEntryController extends Controller
{
    public function store(Request $request, Task $task)
    {
        $this->authorize('create', [TimeEntry::class, $task]);

        $data = $request->validate([
            'started_at' => 'required|date_format:Y-m-d\TH:i',
            'ended_at'   => 'nullable|date_format:Y-m-d\TH:i|after_or_equal:started_at',
            'notes'      => 'nullable|string|max:500',
        ]);

        // Convert from ISO 8601 to database format
        $data['started_at'] = str_replace('T', ' ', $data['started_at']);
        if ($data['ended_at']) {
            $data['ended_at'] = str_replace('T', ' ', $data['ended_at']);
        }

        $data['task_id'] = $task->id;
        $data['created_by'] = auth()->id();

        TimeEntry::create($data);

        return redirect()->route('tasks.show', $task)->with('success', 'Čas přidán.');
    }

    public function update(Request $request, Task $task, TimeEntry $timeEntry)
    {
        $this->authorize('update', $timeEntry);

        $data = $request->validate([
            'started_at' => 'required|date_format:Y-m-d\TH:i',
            'ended_at'   => 'nullable|date_format:Y-m-d\TH:i|after_or_equal:started_at',
            'notes'      => 'nullable|string|max:500',
        ]);

        // Convert from ISO 8601 to database format
        $data['started_at'] = str_replace('T', ' ', $data['started_at']);
        if ($data['ended_at']) {
            $data['ended_at'] = str_replace('T', ' ', $data['ended_at']);
        }

        $timeEntry->update($data);

        return redirect()->route('tasks.show', $task)->with('success', 'Čas upraven.');
    }

    public function destroy(Task $task, TimeEntry $timeEntry)
    {
        $this->authorize('delete', $timeEntry);

        $timeEntry->delete();

        return redirect()->route('tasks.show', $task)->with('success', 'Záznam odstraněn.');
    }
}
