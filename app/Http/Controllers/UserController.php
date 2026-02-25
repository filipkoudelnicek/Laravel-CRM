<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Only admins may manage users.
     */
    private function authorizeAdmin(): void
    {
        abort_unless(Auth::user()?->isAdmin(), 403);
    }

    /**
     * List all users.
     */
    public function index()
    {
        $this->authorizeAdmin();

        $users = User::orderBy('name')->get();

        return view('laravel-examples.user-management', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $this->authorizeAdmin();

        return view('laravel-examples.user-form', [
            'user'  => null,
            'roles' => $this->roles(),
        ]);
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $data = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'max:100', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role'     => ['required', Rule::in(array_keys($this->roles()))],
            'phone'    => ['nullable', 'string', 'max:50'],
            'location' => ['nullable', 'string', 'max:70'],
        ]);

        $data['password'] = Hash::make($data['password']);

        User::create($data);

        return redirect()->route('user-management')->with('success', 'Uživatel byl vytvořen.');
    }

    /**
     * Show the form for editing an existing user.
     */
    public function edit(User $user)
    {
        $this->authorizeAdmin();

        return view('laravel-examples.user-form', [
            'user'  => $user,
            'roles' => $this->roles(),
        ]);
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user)
    {
        $this->authorizeAdmin();

        $data = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'max:100', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role'     => ['required', Rule::in(array_keys($this->roles()))],
            'phone'    => ['nullable', 'string', 'max:50'],
            'location' => ['nullable', 'string', 'max:70'],
        ]);

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return redirect()->route('user-management')->with('success', 'Uživatel byl upraven.');
    }

    /**
     * Delete the specified user.
     */
    public function destroy(User $user)
    {
        $this->authorizeAdmin();

        // Prevent self-deletion
        if ($user->id === Auth::id()) {
            return redirect()->route('user-management')->with('error', 'Nemůžete smazat sami sebe.');
        }

        $user->delete();

        return redirect()->route('user-management')->with('success', 'Uživatel byl smazán.');
    }

    /**
     * Available roles.
     */
    private function roles(): array
    {
        return [
            'admin'  => 'Administrátor',
            'member' => 'Člen',
        ];
    }
}
