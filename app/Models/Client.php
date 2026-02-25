<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'email', 'phone', 'company', 'address', 'notes', 'created_by',
    ];

    // ── relationships ─────────────────────────────────────────

    public function creator(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function projects(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function passwordEntries(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PasswordEntry::class);
    }

    public function invoices(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function supportPlans(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SupportPlan::class);
    }

    // ── invoice helpers ────────────────────────────────────────

    public function totalInvoiced(): float
    {
        return (float) $this->invoices()->sum('total');
    }

    public function totalPaid(): float
    {
        return (float) $this->invoices()->where('status', 'paid')->sum('total');
    }

    public function totalOutstanding(): float
    {
        return (float) $this->invoices()->whereIn('status', ['sent', 'overdue'])->sum('total');
    }
}
