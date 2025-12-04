<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ChangeUserRole extends Command
{
    protected $signature = 'user:role {email : Email uživatele} {role : Role (ADMIN nebo USER)}';

    protected $description = 'Změna role uživatele';

    public function handle()
    {
        $email = $this->argument('email');
        $role = strtoupper($this->argument('role'));

        if (!in_array($role, ['ADMIN', 'USER'])) {
            $this->error('Role musí být ADMIN nebo USER');
            return 1;
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("Uživatel s emailem '{$email}' nebyl nalezen.");
            return 1;
        }

        $oldRole = $user->role;
        $user->role = $role;
        $user->save();

        $this->info("Role uživatele '{$user->name}' ({$email}) byla změněna z '{$oldRole}' na '{$role}'.");
        
        return 0;
    }
}
