<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ClientController extends Controller
{
    public function index(): View
    {
        $clients = Client::with('projects')->latest()->get();
        
        return view('clients.index', compact('clients'));
    }

    public function create(): View
    {
        return view('clients.create');
    }

    public function edit(Client $client): View
    {
        return view('clients.edit', compact('client'));
    }

    public function show(Client $client): View
    {
        $client->load('projects.tasks');
        
        return view('clients.show', compact('client'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        Client::create($validated);

        return redirect()->route('clients.index')
            ->with('success', 'Klient byl úspěšně vytvořen.');
    }

    public function update(Request $request, Client $client): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $client->update($validated);

        return redirect()->route('clients.index')
            ->with('success', 'Klient byl úspěšně aktualizován.');
    }

    public function destroy(Client $client): RedirectResponse
    {
        $client->delete();

        return redirect()->route('clients.index')
            ->with('success', 'Klient byl úspěšně smazán.');
    }
}

