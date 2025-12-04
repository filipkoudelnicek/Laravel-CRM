<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'project_id',
        'status',
        'priority',
        'created_by_id',
        'assigned_to_id',
        'due_date',
    ];

    protected $casts = [
        'due_date' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to_id');
    }

    public function timeEntries()
    {
        return $this->hasMany(TimeEntry::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->whereNull('parent_comment_id');
    }

    public function allComments()
    {
        return $this->hasMany(Comment::class);
    }
}

