<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id', 'title', 'price', 'currency', 'period_from', 'period_to',
        'status', 'notes', 'created_by',
    ];

    protected $casts = [
        'period_from' => 'date',
        'period_to'   => 'date',
        'price'       => 'decimal:2',
    ];

    public const STATUSES = ['active', 'expired', 'cancelled'];

    // ── relationships ─────────────────────────────────────────

    public function client(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function creator(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ── scopes ────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                     ->where('period_to', '>=', now()->toDateString());
    }

    public function scopeExpiringSoon($query, int $days = 30)
    {
        return $query->where('status', 'active')
                     ->whereBetween('period_to', [now()->toDateString(), now()->addDays($days)->toDateString()]);
    }

    public function scopeExpired($query)
    {
        return $query->where(function ($q) {
            $q->where('status', 'expired')
              ->orWhere(function ($q2) {
                  $q2->where('status', 'active')
                     ->where('period_to', '<', now()->toDateString());
              });
        });
    }

    public function isExpiringSoon(int $days = 30): bool
    {
        return $this->status === 'active'
            && $this->period_to
            && $this->period_to->between(now(), now()->addDays($days));
    }
}
