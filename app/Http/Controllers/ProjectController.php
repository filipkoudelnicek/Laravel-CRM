<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use App\Notifications\ProjectAssignedNotification;
use App\Notifications\StatusChangedNotification;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Project::class);

        $query = Project::with('client');

        if (!auth()->user()->isAdmin()) {
            $query->whereHas('users', fn ($q) => $q->where('user_id', auth()->id()));
        }

        if ($search = $request->q) {
            $query->where(fn ($q) =>
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
            );
        }

        if ($status = $request->status) {
            $query->where('status', $status);
        }

        $projects = $query->withCount('tasks')->orderByDesc('created_at')->paginate(15)->withQueryString();

        return view('crm.projects.index', compact('projects'));
    }

    public function create()
    {
        $this->authorize('create', Project::class);
        $clients = Client::orderBy('name')->get();
        return view('crm.projects.create', compact('clients'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Project::class);

        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'status'      => 'required|in:planned,active,on_hold,done',
            'due_date'    => 'nullable|date',
            'client_id'   => 'required|exists:clients,id',
        ]);

        $data['created_by'] = auth()->id();
        $project = Project::create($data);

        // Auto-assign creator
        $project->users()->attach(auth()->id(), ['role' => 'lead']);

        return redirect()->route('projects.show', $project)->with('success', 'Projekt vytvořen.');
    }

    public function show(Project $project)
    {
        $this->authorize('view', $project);
        $project->load(['client', 'users', 'tasks' => fn ($q) => $q->orderByDesc('created_at')]);
        $allUsers = User::orderBy('name')->get();
        return view('crm.projects.show', compact('project', 'allUsers'));
    }

    public function edit(Project $project)
    {
        $this->authorize('update', $project);
        $clients = Client::orderBy('name')->get();
        return view('crm.projects.edit', compact('project', 'clients'));
    }

    public function update(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'description'    => 'nullable|string|max:5000',
            'status'         => 'required|in:planned,active,on_hold,done',
            'due_date'       => 'nullable|date',
            'client_id'      => 'required|exists:clients,id',
            'estimated_cost' => 'nullable|numeric|min:0',
            'hourly_rate'    => 'nullable|numeric|min:0',
        ]);

        $oldStatus = $project->status;
        $project->update($data);

        // Notify assigned users on status change
        if ($oldStatus !== $project->status) {
            $project->users->reject(fn ($u) => $u->id === auth()->id())
                ->each->notify(new StatusChangedNotification($project, $oldStatus, $project->status, auth()->user()));
        }

        return redirect()->route('projects.show', $project)->with('success', 'Projekt upraven.');
    }

    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Projekt smazán.');
    }

    // ── Member management ─────────────────────────────────────

    public function attachUser(Request $request, Project $project)
    {
        $this->authorize('manageMembers', $project);

        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role'    => 'nullable|in:lead,member,viewer',
        ]);

        $project->users()->syncWithoutDetaching([
            $data['user_id'] => ['role' => $data['role'] ?? 'member'],
        ]);

        // Notify the user that they were assigned
        $assignedUser = User::find($data['user_id']);
        if ($assignedUser && $assignedUser->id !== auth()->id()) {
            $assignedUser->notify(new ProjectAssignedNotification($project, auth()->user()));
        }

        return back()->with('success', 'Člen přidán.');
    }

    public function detachUser(Request $request, Project $project, User $user)
    {
        $this->authorize('manageMembers', $project);
        $project->users()->detach($user->id);
        return back()->with('success', 'Člen odebrán.');
    }

    /**
     * Get modal HTML for AJAX requests
     */
    public function modalView(Project $project)
    {
        $this->authorize('view', $project);
        $project->load(['client', 'users', 'tasks']);
        $project->loadCount('tasks');
        return view('crm.projects._modal_content', compact('project'));
    }
}
