<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectNote;
use Illuminate\Http\Request;

class ProjectNoteController extends Controller
{
    public function store(Request $request, Project $project)
    {
        $this->authorize('update', $project);
        $data = $request->validate(['body' => 'required|string|max:10000']);
        $project->notes()->create($data + ['user_id' => $request->user()->id]);

        return redirect()->route('projects.show', [$project, 'tab' => 'notes'])
            ->with('success', 'Poznámka přidána.');
    }

    public function destroy(Request $request, Project $project, ProjectNote $note)
    {
        $this->authorize('view', $project);
        abort_unless($note->project_id === $project->id, 404);
        abort_unless($request->user()->isAdmin() || $note->user_id === $request->user()->id, 403);
        $note->delete();

        return redirect()->route('projects.show', [$project, 'tab' => 'notes'])
            ->with('success', 'Poznámka odstraněna.');
    }
}
