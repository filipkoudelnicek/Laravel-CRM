<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'username',
        'password',
        'url',
        'notes',
        'created_by_id',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'password_entry_users')
            ->withTimestamps();
    }
}

