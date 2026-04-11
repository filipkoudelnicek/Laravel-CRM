<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\PasswordEntry;
use App\Models\PasswordEntryAccessLog;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class PasswordEntryController extends Controller
{
    public function index(Request $request)
    {
        $query = PasswordEntry::with(['client', 'project', 'creator']);

        if (!auth()->user()->isAdmin()) {
            $query->where(fn ($q) =>
                $q->where('created_by', auth()->id())
                  ->orWhereHas('project.users', fn ($pq) => $pq->where('user_id', auth()->id()))
            );
        }

        if ($search = $request->q) {
            $query->where('title', 'like', "%{$search}%");
        }

        $entries = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        return view('crm.passwords.index', compact('entries'));
    }

    public function create()
    {
        $clients  = Client::orderBy('name')->get();
        $projects = Project::orderBy('name')->get();
        $types    = \App\Models\PasswordEntry::TYPES;
        return view('crm.passwords.create', compact('clients', 'projects', 'types'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'      => 'required|string|max:255',
            'type'       => 'required|in:general,sftp,admin,hosting',
            'username'   => 'nullable|string|max:255',
            'password'   => 'required|string|max:1000',
            'url'        => 'nullable|url|max:500',
            'notes'      => 'nullable|string|max:2000',
            'client_id'  => 'nullable|exists:clients,id',
            'project_id' => 'nullable|exists:projects,id',
            // SFTP fields
            'sftp_host'  => 'nullable|string|max:255',
            'sftp_port'  => 'nullable|numeric|min:1|max:65535',
            'sftp_path'  => 'nullable|string|max:500',
            // Hosting fields
            'hosting_provider' => 'nullable|string|max:255',
            'ftp_host'   => 'nullable|string|max:255',
        ]);

        $plain = $data['password'];
        unset($data['password']);

        $data['password_encrypted'] = Crypt::encryptString($plain);
        $data['created_by']         = auth()->id();

        $entry = PasswordEntry::create($data);

        return redirect()->route('passwords.show', $entry)->with('success', 'Záznam uložen.');
    }

    public function show(PasswordEntry $password)
    {
        $this->authorize('view', $password);
        $password->load(['client', 'project', 'creator', 'accessLogs.user']);
        return view('crm.passwords.show', compact('password'));
    }

    public function edit(PasswordEntry $password)
    {
        $this->authorize('update', $password);
        $clients  = Client::orderBy('name')->get();
        $types    = \App\Models\PasswordEntry::TYPES;
        return view('crm.passwords.edit', compact('password', 'clients', 'projects', 'type
        return view('crm.passwords.edit', compact('password', 'clients', 'projects'));
    }

    public function update(Request $request, PasswordEntry $password)
    {
        $this->authorize('update', $password);

        $data = $request->validate([
            'title'      => 'required|string|max:255',
            'type'       => 'required|in:general,sftp,admin,hosting',
            'username'   => 'nullable|string|max:255',
            'password'   => 'nullable|string|max:1000',
            'url'        => 'nullable|url|max:500',
            'notes'      => 'nullable|string|max:2000',
            'client_id'  => 'nullable|exists:clients,id',
            'project_id' => 'nullable|exists:projects,id',
            // SFTP fields
            'sftp_host'  => 'nullable|string|max:255',
            'sftp_port'  => 'nullable|numeric|min:1|max:65535',
            'sftp_path'  => 'nullable|string|max:500',
            // Hosting fields
            'hosting_provider' => 'nullable|string|max:255',
            'ftp_host'   => 'nullable|string|max:255',
        ]);

        if (!empty($data['password'])) {
            $data['password_encrypted'] = Crypt::encryptString($data['password']);
        }
        unset($data['password']);

        $password->update($data);

        return redirect()->route('passwords.show', $password)->with('success', 'Záznam upraven.');
    }

    public function destroy(PasswordEntry $password)
    {
        $this->authorize('delete', $password);
        $password->delete();
        return redirect()->route('passwords.index')->with('success', 'Záznam smazán.');
    }

    // ── Reveal endpoint ───────────────────────────────────────

    public function reveal(Request $request, PasswordEntry $password)
    {
        $this->authorize('reveal', $password);

        $action = in_array($request->query('action'), ['copy']) ? 'copy' : 'reveal';

        PasswordEntryAccessLog::create([
            'user_id'           => auth()->id(),
            'password_entry_id' => $password->id,
            'action'            => $action,
            'ip'                => $request->ip(),
            'user_agent'        => $request->userAgent(),
            'created_at'        => now(),
        ]);

        // For copy action we just log, no need to return the password
        if ($action === 'copy') {
            return response()->json(['ok' => true]);
        }

        return response()->json([
            'password' => Crypt::decryptString($password->password_encrypted),
        ]);
    }
}
