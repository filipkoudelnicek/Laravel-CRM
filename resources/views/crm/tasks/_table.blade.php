@php $statusLabels=['todo'=>'K řešení','in_progress'=>'Probíhá','review'=>'Ke kontrole','done'=>'Dokončeno']; @endphp
<table class="data-table task-table {{ ($compact ?? false) ? 'compact-table' : '' }}" aria-label="Seznam úkolů">
  <thead><tr><th class="primary-cell"><span class="table-head-label">Úkol</span></th><th class="status-cell" style="width:140px"><span class="table-head-label">Stav</span></th><th class="due-cell hide-mobile" style="width:120px"><span class="table-head-label">Termín</span></th><th class="assignee-cell hide-tablet" style="width:86px"><span class="table-head-label">Řeší</span></th><th class="action-cell"><span class="visually-hidden">Akce</span></th></tr></thead>
  <tbody>
    @forelse($tasks as $task)
    <tr>
      <td class="task-title-cell record-cell"><div class="task-title-line"><span class="task-state-dot" data-status="{{ $task->status }}" aria-hidden="true"></span><a class="record-link" href="{{ route('tasks.show',$task) }}" title="{{ $task->title }}">{{ $task->title }}</a>@if($task->priority === 'high')<span class="task-priority" title="Vysoká priorita" aria-label="Vysoká priorita">!</span>@endif</div>@unless($contextProject ?? false)<span class="record-secondary" title="{{ $task->project->client->name }} / {{ $task->project->name }}">{{ $task->project->client->name }} / {{ $task->project->name }}</span>@endunless</td>
      <td class="status-cell">@can('update',$task)<form action="{{ route('tasks.status',$task) }}" method="POST" class="status-form" data-status-form>@csrf @method('PATCH')<select name="status" class="form-select status-select" data-status="{{ $task->status }}" aria-label="Stav úkolu: {{ $task->title }}">@foreach($statusLabels as $key=>$label)<option value="{{ $key }}" @selected($task->status === $key)>{{ $label }}</option>@endforeach</select><noscript><button class="btn btn-sm">Uložit</button></noscript></form>@else<span class="status-chip" data-status="{{ $task->status }}">{{ $statusLabels[$task->status] }}</span>@endcan</td>
      <td class="due-cell hide-mobile muted-cell numeric"><span @class(['text-danger'=>$task->due_at && $task->status !== 'done' && $task->due_at->toDateString() < now(config('app.display_timezone'))->toDateString()])>{{ $task->due_at?->format('j. n. Y') ?? 'Bez termínu' }}</span></td>
      <td class="assignee-cell hide-tablet"><div class="assignee-stack">@forelse($task->assignees->take(2) as $person)<span class="person-avatar" title="{{ $person->name }}" aria-label="{{ $person->name }}">{{ mb_substr($person->name,0,1) }}</span>@empty<span class="record-secondary">—</span>@endforelse</div></td>
      <td class="action-cell"><x-record-actions :record="$task" resource="tasks" :label="$task->title" /></td>
    </tr>
    @empty
      <tr><td colspan="5"><div class="empty-work"><h3>Žádné odpovídající úkoly</h3><p>Upravte filtry nebo přidejte další krok do projektu.</p><a href="{{ route('tasks.create') }}" class="btn btn-outline-secondary">Vytvořit úkol</a></div></td></tr>
    @endforelse
  </tbody>
</table>
