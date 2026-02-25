<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordEntryAccessLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'password_entry_id', 'action', 'ip', 'user_agent', 'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function passwordEntry(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(PasswordEntry::class);
    }
}
