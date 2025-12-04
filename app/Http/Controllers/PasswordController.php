<?php

namespace App\Http\Controllers;

use App\Models\PasswordEntry;
use App\Services\PasswordEncryptionService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PasswordController extends Controller
{
    protected PasswordEncryptionService $encryptionService;

    public function __construct(PasswordEncryptionService $encryptionService)
    {
        $this->encryptionService = $encryptionService;
    }

    public function index(): View
    {
        $query = PasswordEntry::with(['createdBy', 'users']);

        if (!auth()->user()->isAdmin()) {
            $query->where(function($q) {
                $q->where('created_by_id', auth()->id())
                  ->orWhereHas('users', function($q) {
                      $q->where('user_id', auth()->id());
                  });
            });
        }

        $passwords = $query->latest()->get();

        // Dešifrování hesel pro zobrazení
        foreach ($passwords as $password) {
            $password->password = $this->encryptionService->decrypt($password->password);
        }

        return view('passwords.index', compact('passwords'));
    }

    public function create(): View
    {
        $users = \App\Models\User::all();
        return view('passwords.create', compact('users'));
    }

    public function edit(PasswordEntry $password): View
    {
        if (!auth()->user()->isAdmin() && $password->created_by_id !== auth()->id() && !$password->users->contains(auth()->id())) {
            abort(403);
        }

        $password->password = $this->encryptionService->decrypt($password->password);
        $users = \App\Models\User::all();
        return view('passwords.edit', compact('password', 'users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'password' => 'required|string',
            'url' => 'nullable|url',
            'notes' => 'nullable|string',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $validated['password'] = $this->encryptionService->encrypt($validated['password']);
        $validated['created_by_id'] = auth()->id();

        $passwordEntry = PasswordEntry::create($validated);

        if ($request->filled('user_ids')) {
            $passwordEntry->users()->attach($request->user_ids);
        }

        return redirect()->route('passwords.index')
            ->with('success', 'Heslo bylo úspěšně vytvořeno.');
    }

    public function update(Request $request, PasswordEntry $password): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'password' => 'required|string',
            'url' => 'nullable|url',
            'notes' => 'nullable|string',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $validated['password'] = $this->encryptionService->encrypt($validated['password']);
        $password->update($validated);

        if ($request->filled('user_ids')) {
            $password->users()->sync($request->user_ids);
        }

        return redirect()->route('passwords.index')
            ->with('success', 'Heslo bylo úspěšně aktualizováno.');
    }

    public function destroy(PasswordEntry $password): RedirectResponse
    {
        $password->delete();

        return redirect()->route('passwords.index')
            ->with('success', 'Heslo bylo úspěšně smazáno.');
    }
}

