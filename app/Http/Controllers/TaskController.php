<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Notifications\StatusChangedNotification;
use App\Notifications\TaskAssignedNotification;
use App\Support\RichTextSanitizer;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $query = Task::with(['project.client', 'assignees']);

        if (!$user->isAdmin()) {
            $query->whereHas('project.users', fn ($q) => $q->where('user_id', $user->id));
        }

        if ($search = $request->q) {
            $query->where('title', 'like', "%{$search}%");
        }

        if ($status = $request->status) {
            $query->where('status', $status);
        }

        if ($priority = $request->priority) {
            $query->where('priority', $priority);
        }

        $tasks = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        return view('crm.tasks.index', compact('tasks'));
    }

    public function create(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $projects = $user->isAdmin()
            ? Project::with('client')->orderBy('name')->get()
            : $user->projects()->with('client')->orderBy('name')->get();

        $selectedProject = $request->project_id
            ? Project::find($request->project_id)
            : null;

        $allUsers = User::orderBy('name')->get();

        return view('crm.tasks.create', compact('projects', 'selectedProject', 'allUsers'));
    }

    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'status'      => 'required|in:todo,in_progress,review,done',
            'priority'    => 'required|in:low,medium,high',
            'starts_at'   => 'nullable|date|required_with:due_at',
            'due_at'      => 'nullable|date|after_or_equal:starts_at',
            'project_id'  => 'required|exists:projects,id',
            'assignees'   => 'nullable|array',
            'assignees.*' => 'exists:users,id',
            'attachments'   => 'nullable|array|max:8',
            'attachments.*' => 'file|max:10240|mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip',
        ]);

        $data['description'] = RichTextSanitizer::sanitize($data['description'] ?? null);

        $project = Project::findOrFail($data['project_id']);

        if (!$user->isAdmin() && !$project->hasUser($user->id)) {
            abort(403);
        }

        $data['created_by'] = auth()->id();
        $assignees = $data['assignees'] ?? [];
        unset($data['assignees']);

        $task = Task::create($data);

        // Attach assignees
        $sync = [];
        foreach ($assignees as $uid) {
            $sync[$uid] = ['assigned_by' => auth()->id()];
        }
        if ($sync) {
            $task->assignees()->sync($sync);

            // Notify new assignees
            User::whereIn('id', $assignees)
                ->where('id', '!=', auth()->id())
                ->get()
                ->each->notify(new TaskAssignedNotification($task, auth()->user()));
        }

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $task->attachments()->create([
                    'uploaded_by' => auth()->id(),
                    'path' => $file->store('task-attachments/' . $task->id, 'public'),
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize() ?? 0,
                ]);
            }
        }

        return redirect()->route('tasks.show', $task)->with('success', 'Úkol vytvořen.');
    }

    public function show(Task $task)
    {
        $this->authorize('view', $task);

        $task->load([
            'project.client',
            'assignees',
            'attachments',
            'timeEntries.createdBy',
            'comments.user',
            'comments.attachments',
            'comments.replies.user',
            'comments.replies.attachments',
            'comments.replies.replies.user',
            'comments.replies.replies.attachments',
        ]);

        $commentCount = $task->allComments()->count();

        $allUsers = User::orderBy('name')->get();

        return view('crm.tasks.show', compact('task', 'allUsers', 'commentCount'));
    }

    public function edit(Task $task)
    {
        $this->authorize('update', $task);

        /** @var \App\Models\User $user */
        $user = auth()->user();
        $projects = $user->isAdmin()
            ? Project::with('client')->orderBy('name')->get()
            : $user->projects()->with('client')->get();

        $allUsers = User::orderBy('name')->get();

        return view('crm.tasks.edit', compact('task', 'projects', 'allUsers'));
    }

    public function update(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'status'      => 'required|in:todo,in_progress,review,done',
            'priority'    => 'required|in:low,medium,high',
            'starts_at'   => 'nullable|date|required_with:due_at',
            'due_at'      => 'nullable|date|after_or_equal:starts_at',
            'project_id'  => 'required|exists:projects,id',
            'assignees'   => 'nullable|array',
            'assignees.*' => 'exists:users,id',
            'attachments'   => 'nullable|array|max:8',
            'attachments.*' => 'file|max:10240|mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip',
        ]);

        $data['description'] = RichTextSanitizer::sanitize($data['description'] ?? null);

        $assignees = $data['assignees'] ?? [];
        unset($data['assignees']);

        $oldStatus    = $task->status;
        $oldAssignees = $task->assignees->pluck('id')->toArray();

        $task->update($data);

        $sync = [];
        foreach ($assignees as $uid) {
            $sync[$uid] = ['assigned_by' => auth()->id()];
        }
        $task->assignees()->sync($sync);

        // Notify newly assigned users
        $newAssignees = array_diff($assignees, $oldAssignees);
        if (!empty($newAssignees)) {
            User::whereIn('id', $newAssignees)
                ->where('id', '!=', auth()->id())
                ->get()
                ->each->notify(new TaskAssignedNotification($task, auth()->user()));
        }

        // Notify on status change
        if ($oldStatus !== $task->status) {
            $task->assignees->reject(fn ($u) => $u->id === auth()->id())
                ->each->notify(new StatusChangedNotification($task, $oldStatus, $task->status, auth()->user()));
        }

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $task->attachments()->create([
                    'uploaded_by' => auth()->id(),
                    'path' => $file->store('task-attachments/' . $task->id, 'public'),
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize() ?? 0,
                ]);
            }
        }

        return redirect()->route('tasks.show', $task)->with('success', 'Úkol upraven.');
    }

    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);
        $task->delete();
        return redirect()->route('tasks.index')->with('success', 'Úkol smazán.');
    }

    /**
     * Get modal HTML for AJAX requests
     */
    public function modalView(Task $task)
    {
        $this->authorize('view', $task);
        $task->load(['project.client', 'assignees']);
        return view('crm.tasks._modal_content', compact('task'));
    }
}
