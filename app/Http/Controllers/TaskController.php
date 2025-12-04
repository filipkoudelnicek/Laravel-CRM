<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use App\Notifications\TaskAssigned;
use App\Notifications\TaskUpdated;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class TaskController extends Controller
{
    public function index(Request $request): View
    {
        $query = Task::with(['project.client', 'assignedTo', 'createdBy']);

        // Filtrování
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('assigned_to_id')) {
            $query->where('assigned_to_id', $request->assigned_to_id);
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        $tasks = $query->latest()->get();
        $projects = Project::with('client')->get();
        $users = User::all();
        $view = $request->get('view', 'list'); // list nebo kanban

        return view('tasks.index', compact('tasks', 'projects', 'users', 'view'));
    }

    public function create(): View
    {
        $projects = Project::with('client')->get();
        $users = User::all();
        return view('tasks.create', compact('projects', 'users'));
    }

    public function show(Task $task): View
    {
        $task->load([
            'project.client',
            'assignedTo',
            'createdBy',
            'comments.user',
            'comments.replies.user',
            'comments.attachments'
        ]);

        $users = User::all();

        return view('tasks.show', compact('task', 'users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'project_id' => 'required|exists:projects,id',
            'status' => 'required|in:todo,in_progress,pending_approval,done',
            'priority' => 'required|in:low,medium,high',
            'assigned_to_id' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
        ]);

        $validated['created_by_id'] = auth()->id();

        $task = Task::create($validated);

        // Notifikace při přiřazení
        if ($task->assigned_to_id && $task->assigned_to_id !== auth()->id()) {
            $assignedUser = User::find($task->assigned_to_id);
            $assignedUser->notify(new TaskAssigned($task));
        }

        return redirect()->route('tasks.show', $task)
            ->with('success', 'Úkol byl úspěšně vytvořen.');
    }

    public function edit(Task $task): View
    {
        $projects = Project::with('client')->get();
        $users = User::all();
        return view('tasks.edit', compact('task', 'projects', 'users'));
    }

    public function update(Request $request, Task $task): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:todo,in_progress,pending_approval,done',
            'priority' => 'required|in:low,medium,high',
            'assigned_to_id' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
        ]);

        $oldAssignedTo = $task->assigned_to_id;
        $task->update($validated);

        // Notifikace při změně přiřazení
        if ($task->assigned_to_id !== $oldAssignedTo && $task->assigned_to_id) {
            $assignedUser = User::find($task->assigned_to_id);
            $assignedUser->notify(new TaskAssigned($task));
        }

        // Notifikace při aktualizaci
        if ($task->assigned_to_id && $task->assigned_to_id !== auth()->id()) {
            $assignedUser = User::find($task->assigned_to_id);
            $assignedUser->notify(new TaskUpdated($task));
        }

        return redirect()->route('tasks.show', $task)
            ->with('success', 'Úkol byl úspěšně aktualizován.');
    }

    public function updateStatus(Request $request, Task $task): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:todo,in_progress,pending_approval,done',
        ]);

        $task->update($validated);

        return response()->json(['success' => true]);
    }

    public function destroy(Task $task): RedirectResponse
    {
        $task->delete();

        return redirect()->route('tasks.index')
            ->with('success', 'Úkol byl úspěšně smazán.');
    }
}
