@php
    $startTimeString = null;
    if ($startTime) {
        if ($startTime instanceof \Carbon\Carbon) {
            $startTimeString = $startTime->toIso8601String();
        } elseif (is_string($startTime)) {
            $startTimeString = $startTime;
        } else {
            $startTimeString = \Carbon\Carbon::parse($startTime)->toIso8601String();
        }
    }
@endphp
<div x-data="{
         elapsed: {{ $elapsed ?? 0 }},
         isRunning: @js($isRunning ?? false),
         isPaused: @js($isPaused ?? false),
         startTime: @js($startTimeString),
         intervalId: null,
         init() {
             console.log('Alpine init', { isRunning: this.isRunning, isPaused: this.isPaused, startTime: this.startTime, elapsed: this.elapsed });
             
             // Poslouchat Livewire eventy
             window.addEventListener('timer-started', (event) => {
                 console.log('Timer started event', event);
                 // Livewire 3 posílá data jako pole v event.detail
                 const data = Array.isArray(event.detail) ? event.detail[0] : event.detail;
                 console.log('Event data', data);
                 if (data && data.startTime) {
                     this.startTime = data.startTime;
                     this.isRunning = true;
                     this.isPaused = false;
                     this.elapsed = 0;
                     console.log('Starting timer with startTime:', this.startTime, 'isRunning:', this.isRunning);
                     this.startTimer();
                 }
             });
             
             // Inicializace pokud už běží
             setTimeout(() => {
                 console.log('Checking if timer should start', { isRunning: this.isRunning, isPaused: this.isPaused, startTime: this.startTime });
                 if (this.isRunning && !this.isPaused && this.startTime) {
                     console.log('Starting timer on init');
                     this.startTimer();
                 }
             }, 200);
         },
         updateTime() {
             if (this.isRunning && !this.isPaused && this.startTime) {
                 try {
                     const start = new Date(this.startTime);
                     const now = new Date();
                     const diff = Math.floor((now - start) / 1000);
                     this.elapsed = Math.max(0, diff);
                 } catch(e) {
                     console.error('Error updating time:', e, 'startTime:', this.startTime);
                     this.elapsed = 0;
                 }
             }
         },
         formatTime(seconds) {
             const hours = Math.floor(seconds / 3600);
             const minutes = Math.floor((seconds % 3600) / 60);
             const secs = seconds % 60;
             return String(hours).padStart(2, '0') + ':' + 
                    String(minutes).padStart(2, '0') + ':' + 
                    String(secs).padStart(2, '0');
         },
         startTimer() {
             console.log('startTimer called', { intervalId: this.intervalId, isRunning: this.isRunning, isPaused: this.isPaused, startTime: this.startTime });
             if (this.intervalId) {
                 clearInterval(this.intervalId);
             }
             this.updateTime();
             this.intervalId = setInterval(() => {
                 this.updateTime();
             }, 1000);
             console.log('Timer started, intervalId:', this.intervalId);
         },
         stopTimer() {
             if (this.intervalId) {
                 clearInterval(this.intervalId);
                 this.intervalId = null;
             }
         }
     }">
    <div class="flex items-center gap-3 {{ $isRunning && !$isPaused ? 'bg-indigo-600' : 'bg-gray-800' }} rounded-lg px-4 py-2">
        <div class="flex items-center gap-3">
            @if(!$isRunning)
                <button type="button"
                        wire:click="start" 
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-50 cursor-not-allowed"
                        class="flex items-center gap-2 px-3 py-1.5 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition-colors text-sm font-medium">
                    <svg wire:loading.remove wire:target="start" class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z" />
                    </svg>
                    <svg wire:loading wire:target="start" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span wire:loading.remove wire:target="start">Start</span>
                    <span wire:loading wire:target="start">Spouštím...</span>
                </button>
            @else
                @if($isPaused)
                    <button wire:click="resume" 
                            class="flex items-center gap-2 px-3 py-1.5 bg-green-600 text-white rounded hover:bg-green-700 transition-colors text-sm font-medium">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z" />
                        </svg>
                    </button>
                @else
                    <button wire:click="pause" 
                            class="flex items-center gap-2 px-3 py-1.5 bg-yellow-600 text-white rounded hover:bg-yellow-700 transition-colors text-sm font-medium">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zM7 8a1 1 0 012 0v4a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v4a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </button>
                @endif
                <button wire:click="stop" 
                        class="flex items-center gap-2 px-3 py-1.5 bg-red-600 text-white rounded hover:bg-red-700 transition-colors text-sm font-medium">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8 7a1 1 0 00-1 1v4a1 1 0 001 1h4a1 1 0 001-1V8a1 1 0 00-1-1H8z" clip-rule="evenodd" />
                    </svg>
                </button>
            @endif
        </div>
        
        <div class="flex items-center gap-2 border-l border-gray-700 pl-3">
            <p class="text-lg font-mono font-bold {{ $isRunning && !$isPaused ? 'text-white' : 'text-gray-300' }}"
               x-text="formatTime(elapsed)">
                {{ $this->formatTime($elapsed ?? 0) }}
            </p>
            @if($isRunning && $startTime)
                <p class="text-xs text-gray-400 ml-2">
                    @php
                        try {
                            $time = $startTime instanceof \Carbon\Carbon 
                                ? $startTime 
                                : \Carbon\Carbon::parse($startTime);
                            echo $time->format('H:i');
                        } catch (\Exception $e) {
                            echo '--:--';
                        }
                    @endphp
                </p>
            @endif
        </div>
    </div>

    @if($showStopModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" style="display: block;">
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="$set('showStopModal', false)"></div>
            <div class="relative bg-white dark:bg-gray-900 rounded-xl shadow-xl max-w-lg w-full p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Ukončit trackování času</h3>
                <div class="mb-4 p-4 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Trackovaný čas:</p>
                    <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400"
                       x-text="formatTime(elapsed)">
                        {{ $this->formatTime($elapsed ?? 0) }}
                    </p>
                </div>
                <form wire:submit.prevent="stopConfirm" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Co jste dělali?
                        </label>
                        <textarea wire:model="description" rows="4" 
                                  class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                                  placeholder="Popište, na čem jste pracovali..."></textarea>
                    </div>
                    <div class="flex gap-3 pt-4">
                        <button type="submit" 
                                class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium">
                            Ukončit a uložit
                        </button>
                        <button type="button" wire:click="$set('showStopModal', false)" 
                                class="flex-1 px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors font-medium">
                            Zrušit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
