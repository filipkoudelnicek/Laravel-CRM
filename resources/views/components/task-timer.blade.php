@props(['task'])
@if(!$activeTimer)
  <form method="POST" action="{{ route('tasks.timer.start',$task) }}">@csrf<button class="btn btn-primary"><x-icon name="play" />Spustit časovač</button></form>
@elseif($activeTimer->task_id === $task->id)
  <form method="POST" action="{{ route('timers.stop',$activeTimer) }}">@csrf<button class="btn btn-primary"><x-icon name="stop" />Zastavit a uložit</button></form>
@else
  <a class="btn btn-outline-secondary" href="{{ route('tasks.show',$activeTimer->task_id) }}"><x-icon name="clock" />Běžící časovač</a>
@endif
