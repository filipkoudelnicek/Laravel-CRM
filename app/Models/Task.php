<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'status', 'priority', 'starts_at', 'due_at', 'project_id', 'created_by',
    ];

    protected $casts = [
        'starts_at' => 'date',
        'due_at'    => 'date',
    ];

    public const STATUSES   = ['todo', 'in_progress', 'review', 'done'];
    public const PRIORITIES = ['low', 'medium', 'high'];

    // ── relationships ─────────────────────────────────────────

    public function project(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function creator(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignees(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'task_user')
                    ->withPivot('assigned_by')
                    ->withTimestamps();
    }

    public function comments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Comment::class)->whereNull('parent_id')->with('replies.user')->orderBy('created_at');
    }

    public function allComments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function timeEntries(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TimeEntry::class)->orderByDesc('started_at');
    }
}
