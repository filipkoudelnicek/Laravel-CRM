<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function isAdmin(): bool
    {
        return $this->role === 'ADMIN';
    }

    // Relations
    public function timeEntries()
    {
        return $this->hasMany(TimeEntry::class);
    }

    public function workTimeEntries()
    {
        return $this->hasMany(WorkTimeEntry::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function createdTasks()
    {
        return $this->hasMany(Task::class, 'created_by_id');
    }

    public function assignedTasks()
    {
        return $this->hasMany(Task::class, 'assigned_to_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function createdPasswords()
    {
        return $this->hasMany(PasswordEntry::class, 'created_by_id');
    }

    public function passwordEntries()
    {
        return $this->belongsToMany(PasswordEntry::class, 'password_entry_users')
            ->withTimestamps();
    }
}

