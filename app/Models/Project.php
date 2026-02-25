<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'status', 'due_date', 'client_id', 'created_by',
        'estimated_cost', 'hourly_rate',
    ];

    protected $casts = [
        'due_date'       => 'date',
        'estimated_cost' => 'decimal:2',
        'hourly_rate'    => 'decimal:2',
    ];

    public const STATUSES = ['planned', 'active', 'on_hold', 'done'];

    // ── relationships ─────────────────────────────────────────

    public function client(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function creator(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function users(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_user')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    public function tasks(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function passwordEntries(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PasswordEntry::class);
    }

    public function invoices(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    // ── finance helpers ────────────────────────────────────────

    public function trackedCost(): float
    {
        return (float) $this->invoices()->sum('total');
    }

    public function paidTotal(): float
    {
        return (float) $this->invoices()->where('status', 'paid')->sum('total');
    }

    // ── helpers ───────────────────────────────────────────────

    public function hasUser(int $userId): bool
    {
        return $this->users()->where('user_id', $userId)->exists();
    }
}
