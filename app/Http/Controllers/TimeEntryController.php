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
            'ended_at'   => 'required|date_format:Y-m-d\TH:i|after_or_equal:started_at',
            'notes'      => 'nullable|string|max:500',
        ]);

        // Convert from ISO 8601 to database format
        $data['started_at'] = \Carbon\Carbon::createFromFormat('!Y-m-d\TH:i', $data['started_at'], config('app.display_timezone'))->utc();
        if ($data['ended_at']) {
            $data['ended_at'] = \Carbon\Carbon::createFromFormat('!Y-m-d\TH:i', $data['ended_at'], config('app.display_timezone'))->utc();
        }

        $data['task_id'] = $task->id;
        $data['created_by'] = auth()->id();

        TimeEntry::create($data);

        return redirect()->route('tasks.show', $task)->with('success', 'Čas přidán.');
    }

    public function update(Request $request, Task $task, TimeEntry $timeEntry)
    {
        abort_unless($timeEntry->task_id === $task->id, 404);
        $this->authorize('update', $timeEntry);

        $data = $request->validate([
            'started_at' => 'required|date_format:Y-m-d\TH:i',
            'ended_at'   => 'required|date_format:Y-m-d\TH:i|after_or_equal:started_at',
            'notes'      => 'nullable|string|max:500',
        ]);

        // Convert from ISO 8601 to database format
        $data['started_at'] = \Carbon\Carbon::createFromFormat('!Y-m-d\TH:i', $data['started_at'], config('app.display_timezone'))->utc();
        if ($data['ended_at']) {
            $data['ended_at'] = \Carbon\Carbon::createFromFormat('!Y-m-d\TH:i', $data['ended_at'], config('app.display_timezone'))->utc();
        }

        $timeEntry->update($data);

        return redirect()->route('tasks.show', $task)->with('success', 'Čas upraven.');
    }

    public function destroy(Task $task, TimeEntry $timeEntry)
    {
        abort_unless($timeEntry->task_id === $task->id, 404);
        $this->authorize('delete', $timeEntry);

        $timeEntry->delete();

        return redirect()->route('tasks.show', $task)->with('success', 'Záznam odstraněn.');
    }
}
