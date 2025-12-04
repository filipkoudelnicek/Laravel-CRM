<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\WorkTimeEntry;
use App\Models\TimeEntry;

class WorkTimeTracker extends Component
{
    public $isRunning = false;
    public $isPaused = false;
    public $elapsed = 0;
    public $startTime = null;
    public $currentSessionId = null;
    public $showStopModal = false;
    public $description = '';

    public function mount()
    {
        // Lazy load - načte se až když je potřeba
        if (auth()->check()) {
            $this->loadActiveSession();
        }
    }
    
    public function hydrate()
    {
        // Při hydrataci (navigace) se znovu načte session
        if (auth()->check()) {
            $this->loadActiveSession();
        }
    }

    public function loadActiveSession()
    {
        if (!auth()->check()) {
            return;
        }

        $active = WorkTimeEntry::where('user_id', auth()->id())
            ->whereIn('status', ['running', 'paused'])
            ->first();

        if ($active) {
            $this->isRunning = true;
            $this->isPaused = $active->status === 'paused';
            $this->startTime = $active->start_time;
            $this->currentSessionId = $active->id;
            $this->elapsed = 0;
            $this->calculateElapsed(); // Okamžitě vypočítat elapsed čas
        }
    }

    public function start()
    {
        try {
            if (!auth()->check()) {
                session()->flash('error', 'Musíte být přihlášeni.');
                return;
            }

            // Zkontrolovat, zda už existuje aktivní session
            $active = WorkTimeEntry::where('user_id', auth()->id())
                ->whereIn('status', ['running', 'paused'])
                ->first();

            if ($active) {
                // Pokud už existuje aktivní session, načíst ji
                $this->isRunning = true;
                $this->isPaused = $active->status === 'paused';
                $this->startTime = $active->start_time;
                $this->currentSessionId = $active->id;
                $this->calculateElapsed();
                return;
            }

            $workTimeEntry = WorkTimeEntry::create([
                'user_id' => auth()->id(),
                'start_time' => now(),
                'status' => 'running',
            ]);

            $this->isRunning = true;
            $this->isPaused = false;
            $this->startTime = $workTimeEntry->start_time;
            $this->currentSessionId = $workTimeEntry->id;
            $this->elapsed = 0;
            
            // Okamžitě vypočítat elapsed čas
            $this->calculateElapsed();
            
            // Dispatch event pro JavaScript timer
            $this->dispatch('timer-started', [
                'startTime' => $this->startTime->toIso8601String()
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error in start(): ' . $e->getMessage());
            session()->flash('error', 'Chyba při spuštění timeru: ' . $e->getMessage());
        }
    }

    public function pause()
    {
        if (!auth()->check() || !$this->currentSessionId) return;

        $workTimeEntry = WorkTimeEntry::find($this->currentSessionId);
        if ($workTimeEntry && $workTimeEntry->user_id === auth()->id()) {
            $workTimeEntry->update(['status' => 'paused']);
            $this->isPaused = true;
            $this->calculateElapsed(); // Aktualizovat elapsed před pausem
        }
    }

    public function resume()
    {
        if (!auth()->check() || !$this->currentSessionId) return;

        $workTimeEntry = WorkTimeEntry::find($this->currentSessionId);
        if ($workTimeEntry && $workTimeEntry->user_id === auth()->id()) {
            $workTimeEntry->update(['status' => 'running']);
            $this->isPaused = false;
            $this->calculateElapsed(); // Aktualizovat elapsed při resume
        }
    }

    public function stop()
    {
        if (!auth()->check()) return;
        // Aktualizovat elapsed čas před zobrazením modalu
        if ($this->isRunning && !$this->isPaused && $this->startTime) {
            $this->calculateElapsed();
        }
        $this->showStopModal = true;
    }

    public function stopConfirm()
    {
        if (!auth()->check() || !$this->currentSessionId) return;

        $workTimeEntry = WorkTimeEntry::find($this->currentSessionId);
        if (!$workTimeEntry || $workTimeEntry->user_id !== auth()->id()) {
            return;
        }

        $now = now();
        $startTime = $workTimeEntry->start_time;
        $totalSeconds = $now->diffInSeconds($startTime);
        $workSeconds = $totalSeconds - $workTimeEntry->paused_duration;
        $durationMinutes = max(1, ceil($workSeconds / 60));

        $workTimeEntry->update([
            'status' => 'stopped',
            'end_time' => $now,
        ]);

        TimeEntry::create([
            'user_id' => auth()->id(),
            'description' => $this->description,
            'duration' => $durationMinutes,
            'date' => now(),
            'start_time' => $startTime,
            'end_time' => $now,
        ]);

        $this->resetTracker();
        $this->showStopModal = false;
        $this->description = '';
        
        session()->flash('success', 'Časový záznam byl úspěšně uložen.');
        return redirect()->route('time-tracking.index');
    }

    public function resetTracker()
    {
        $this->isRunning = false;
        $this->isPaused = false;
        $this->elapsed = 0;
        $this->startTime = null;
        $this->currentSessionId = null;
    }

    public function calculateElapsed()
    {
        // Pokud není running nebo je paused, nepočítat
        if (!$this->isRunning || $this->isPaused) {
            return;
        }
        
        // Pokud není startTime, nastavit elapsed na 0
        if (!$this->startTime) {
            $this->elapsed = 0;
            return;
        }
        
        try {
            // Zajistit, že startTime je Carbon instance
            if ($this->startTime instanceof \Carbon\Carbon) {
                $start = $this->startTime;
            } elseif (is_string($this->startTime)) {
                $start = \Carbon\Carbon::parse($this->startTime);
            } else {
                // Pokud je to něco jiného, zkusit parsovat
                $start = \Carbon\Carbon::parse((string)$this->startTime);
            }
            
            // Vypočítat rozdíl v sekundách
            $seconds = now()->diffInSeconds($start);
            $this->elapsed = max(0, $seconds);
            
            \Log::debug('calculateElapsed called', [
                'startTime' => $start->toIso8601String(),
                'now' => now()->toIso8601String(),
                'elapsed' => $this->elapsed,
                'isRunning' => $this->isRunning,
                'isPaused' => $this->isPaused
            ]);
        } catch (\Exception $e) {
            // Pokud je problém s parsováním, resetovat
            \Log::error('Error calculating elapsed time: ' . $e->getMessage(), [
                'startTime' => $this->startTime,
                'startTimeType' => gettype($this->startTime)
            ]);
            $this->elapsed = 0;
        }
    }

    public function formatTime($seconds)
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
    }

    public function render()
    {
        // Vždy aktualizovat elapsed čas pokud běží (i při renderu)
        if ($this->isRunning && !$this->isPaused && $this->startTime) {
            $this->calculateElapsed();
        }
        
        // Zajistit, aby se elapsed nezměnil pokud není running
        if (!$this->isRunning) {
            $this->elapsed = 0;
        }

        return view('livewire.work-time-tracker');
    }
}
