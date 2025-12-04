<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Task;
use App\Models\Project;
use App\Models\User;

class TaskKanban extends Component
{
    public $tasks = [];
    public $statuses = [
        'todo' => 'Udělat',
        'in_progress' => 'Probíhá',
        'pending_approval' => 'Ke schválení',
        'done' => 'Dokončeno',
    ];

    public function mount()
    {
        $this->loadTasks();
    }

    public function loadTasks()
    {
        $tasks = Task::with(['project.client', 'assignedTo'])->get();
        $grouped = [];
        
        foreach ($this->statuses as $status => $label) {
            $grouped[$status] = $tasks->where('status', $status)->values()->toArray();
        }
        
        $this->tasks = $grouped;
    }

    public function updateTaskStatus($taskId, $newStatus)
    {
        $task = Task::find($taskId);
        
        if ($task) {
            $task->update(['status' => $newStatus]);
            $this->loadTasks();
            
            // Emit event pro notifikace
            $this->dispatch('taskUpdated', $taskId);
        }
    }

    public function render()
    {
        return view('livewire.task-kanban');
    }
}

