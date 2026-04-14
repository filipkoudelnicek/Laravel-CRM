<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskAttachment;
use Illuminate\Support\Facades\Storage;

class TaskAttachmentController extends Controller
{
    public function destroy(Task $task, TaskAttachment $attachment)
    {
        $this->authorize('update', $task);

        if ($attachment->task_id !== $task->id) {
            abort(404);
        }

        Storage::disk('public')->delete($attachment->path);
        $attachment->delete();

        return back()->with('success', 'Příloha úkolu byla smazána.');
    }
}
