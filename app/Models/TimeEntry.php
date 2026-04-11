<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id', 'started_at', 'ended_at', 'notes', 'created_by',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at'   => 'datetime',
    ];

    // ── relationships ─────────────────────────────────────────

    public function task(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function creator(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ── accessors ──────────────────────────────────────────────

    public function getDurationInMinutesAttribute(): ?int
    {
        if (!$this->ended_at) {
            return null;
        }
        return intval($this->ended_at->diffInMinutes($this->started_at));
    }

    public function getDurationFormattedAttribute(): string
    {
        $minutes = $this->getDurationInMinutesAttribute();
        if (!$minutes) {
            return '—';
        }
        $hours = intval($minutes / 60);
        $mins = $minutes % 60;
        if ($hours > 0) {
            return "{$hours}h {$mins}m";
        }
        return "{$mins}m";
    }
}
