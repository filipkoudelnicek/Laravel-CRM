@if($activeTimer)
<aside class="running-timer" aria-label="Běžící měření času">
  <span class="timer-dot" aria-hidden="true"></span><a href="{{ route('tasks.show', $activeTimer->task_id) }}"><strong>{{ $activeTimer->task->title }}</strong><small>{{ $activeTimer->task->project->name }}</small></a>
  <span class="timer-readout" data-timer-start="{{ $activeTimer->started_at->toIso8601String() }}" data-server-now="{{ now()->toIso8601String() }}" aria-label="Uplynulý čas">{{ $activeTimer->started_at->diff(now())->format('%H:%I:%S') }}</span>
  <form method="POST" action="{{ route('timers.stop', $activeTimer) }}">@csrf<button type="submit" class="btn btn-sm btn-primary mb-0">Zastavit a uložit</button></form>
</aside>
@endif
