@php
  $statusLabels = ['todo'=>'K řešení','in_progress'=>'Probíhá','review'=>'Ke kontrole','done'=>'Dokončeno'];
  $priorityLabels = ['low'=>'Nízká','medium'=>'Střední','high'=>'Vysoká'];
@endphp
<table class="data-table {{ ($compact ?? false) ? 'compact-table' : '' }}" aria-label="Seznam úkolů">
  <thead><tr><th class="primary-cell">Úkol</th><th class="status-cell" style="width:150px">Stav</th><th class="hide-tablet" style="width:100px">Priorita</th><th class="hide-mobile" style="width:110px">Termín</th><th class="hide-lg" style="width:100px">Řeší</th><th class="action-cell"><span class="visually-hidden">Akce</span></th></tr></thead>
  <tbody>
  @forelse($tasks as $task)
    <tr>
      <td><a class="record-link" href="{{ route('tasks.show', $task) }}" title="{{ $task->title }}">{{ $task->title }}</a><span class="record-secondary" title="{{ $task->project->name }}">{{ $task->project->client->name }} / {{ $task->project->name }}</span></td>
      <td class="status-cell">@can('update', $task)<form action="{{ route('tasks.status', $task) }}" method="POST" class="status-form" data-status-form>@csrf @method('PATCH')<select name="status" class="form-select status-select" aria-label="Stav úkolu: {{ $task->title }}">@foreach($statusLabels as $key=>$label)<option value="{{ $key }}" @selected($task->status === $key)>{{ $label }}</option>@endforeach</select><noscript><button class="btn btn-sm" type="submit">Uložit</button></noscript></form>@else<span class="status-chip" data-status="{{ $task->status }}">{{ $statusLabels[$task->status] }}</span>@endcan</td>
      <td class="hide-tablet"><span class="priority-{{ $task->priority }}">{{ $priorityLabels[$task->priority] }}</span></td>
      <td class="hide-mobile muted-cell numeric"><span @class(['text-danger' => $task->due_at && $task->status !== 'done' && $task->due_at->toDateString() < now(config('app.display_timezone'))->toDateString()])>{{ $task->due_at?->format('j. n. Y') ?? '—' }}</span></td>
      <td class="hide-lg"><div class="assignee-stack">@forelse($task->assignees->take(2) as $person)<span class="person-avatar" title="{{ $person->name }}" aria-label="{{ $person->name }}">{{ mb_substr($person->name,0,1) }}</span>@empty<span class="record-secondary">Nepřiřazeno</span>@endforelse @if($task->assignees->count()>2)<span class="record-secondary">+{{ $task->assignees->count()-2 }}</span>@endif</div></td>
      <td class="action-cell"><x-record-actions :record="$task" resource="tasks" :label="$task->title" /></td>
    </tr>
  @empty
    <tr><td colspan="6"><div class="empty-work"><h3>Žádné odpovídající úkoly</h3><p>Upravte filtry nebo vytvořte nový úkol.</p><a href="{{ route('tasks.create') }}" class="btn btn-outline-secondary">Vytvořit úkol</a></div></td></tr>
  @endforelse
  </tbody>
</table>
