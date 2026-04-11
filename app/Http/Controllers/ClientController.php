<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Client::class);

        $query = Client::query();

        // Members only see clients that have projects they're assigned to
        if (!auth()->user()->isAdmin()) {
            $query->whereHas('projects.users', fn ($q) => $q->where('user_id', auth()->id()));
        }

        if ($search = $request->q) {
            $query->where(fn ($q) =>
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
            );
        }

        $clients = $query->withCount('projects')->orderBy('name')->paginate(15)->withQueryString();

        return view('crm.clients.index', compact('clients'));
    }

    public function create()
    {
        $this->authorize('create', Client::class);
        return view('crm.clients.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Client::class);

        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'nullable|email|max:255',
            'phone'   => 'nullable|string|max:50',
            'company' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'notes'   => 'nullable|string|max:2000',
        ]);

        $data['created_by'] = auth()->id();
        $client = Client::create($data);

        return redirect()->route('clients.show', $client)->with('success', 'Klient vytvořen.');
    }

    public function show(Client $client)
    {
        $this->authorize('view', $client);
        $client->load(['projects' => fn ($q) => $q->withCount('tasks')->latest()]);
        return view('crm.clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        $this->authorize('update', $client);
        return view('crm.clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $this->authorize('update', $client);

        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'nullable|email|max:255',
            'phone'   => 'nullable|string|max:50',
            'company' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'notes'   => 'nullable|string|max:2000',
        ]);

        $client->update($data);

        return redirect()->route('clients.show', $client)->with('success', 'Klient upraven.');
    }

    public function destroy(Client $client)
    {
        $this->authorize('delete', $client);
        $client->delete();
        return redirect()->route('clients.index')->with('success', 'Klient smazán.');
    }

    /**
     * Get modal HTML for AJAX requests
     */
    public function modalView(Client $client)
    {
        $this->authorize('view', $client);
        $client->load('projects');
        $client->loadCount('projects');
        return view('crm.clients._modal_content', compact('client'));
    }
}
