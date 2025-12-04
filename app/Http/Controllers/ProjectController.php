<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ProjectController extends Controller
{
    public function index(): View
    {
        $projects = Project::with('client')->latest()->get();
        
        return view('projects.index', compact('projects'));
    }

    public function create(): View
    {
        $clients = \App\Models\Client::all();
        return view('projects.create', compact('clients'));
    }

    public function edit(Project $project): View
    {
        $clients = \App\Models\Client::all();
        return view('projects.edit', compact('project', 'clients'));
    }

    public function show(Project $project): View
    {
        $project->load(['client', 'tasks.assignedTo', 'tasks.createdBy']);
        
        return view('projects.show', compact('project'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'client_id' => 'required|exists:clients,id',
            'status' => 'required|in:active,inactive',
        ]);

        Project::create($validated);

        return redirect()->route('projects.index')
            ->with('success', 'Projekt byl úspěšně vytvořen.');
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'client_id' => 'required|exists:clients,id',
            'status' => 'required|in:active,inactive',
        ]);

        $project->update($validated);

        return redirect()->route('projects.index')
            ->with('success', 'Projekt byl úspěšně aktualizován.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Projekt byl úspěšně smazán.');
    }
}

