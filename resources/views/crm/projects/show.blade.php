@extends('layouts.user_type.auth')
@section('title', $project->name)
@section('content')
@php
  $statuses = ['planned'=>'Plánovaný','active'=>'Aktivní','on_hold'=>'Pozastavený','done'=>'Dokončený'];
  $taskStatuses = ['todo'=>'K řešení','in_progress'=>'Probíhá','review'=>'Ke kontrole','done'=>'Dokončeno'];
  $entries = $project->tasks->flatMap->timeEntries;
  $totalMinutes = (int) $entries->sum(fn($e) => $e->duration_in_minutes ?? 0);
@endphp
<div class="workspace-heading">
  <div><a class="context-line" href="{{ route('clients.show', $project->client) }}">{{ $project->client->name }}</a><h1>{{ $project->name }}</h1><div class="d-flex gap-3 align-items-center flex-wrap"><span class="status-label">{{ $statuses[$project->status] }}</span><span class="text-secondary text-sm">{{ $project->due_date ? 'Termín ' . $project->due_date->format('j. n. Y') : 'Bez pevného termínu' }}</span></div></div>
  <div class="d-flex gap-2 flex-wrap">@can('update', $project)<a href="{{ route('projects.edit', $project) }}" class="btn btn-outline-secondary">Upravit projekt</a>@endcan<a href="{{ route('tasks.create', ['project_id' => $project->id]) }}" class="btn btn-primary">+ Nový úkol</a></div>
</div>
<nav class="work-tabs" aria-label="Sekce projektu">
  @foreach(['tasks'=>'Úkoly','notes'=>'Poznámky','time'=>'Odpracovaný čas','overview'=>'Přehled a služby'] as $key=>$label)
    <a href="{{ route('projects.show', [$project, 'tab' => $key]) }}" class="{{ $tab === $key ? 'active' : '' }}" @if($tab === $key) aria-current="page" @endif>{{ $label }}@if($key === 'tasks')<span>{{ $project->tasks->count() }}</span>@elseif($key === 'notes')<span>{{ $notes->total() }}</span>@endif</a>
  @endforeach
</nav>
@if($tab === 'tasks')
<section class="card work-panel">
  <header class="panel-heading"><div><h2>Další kroky</h2><p>{{ $project->tasks->where('status', 'done')->count() }} z {{ $project->tasks->count() }} úkolů dokončeno</p></div></header>
  @forelse($project->tasks as $task)
  <a class="work-row" href="{{ route('tasks.show', $task) }}"><span class="task-indicator status-{{ $task->status }}" aria-hidden="true"></span><span class="row-content"><strong>{{ $task->title }}</strong><small>{{ $task->assignees->pluck('name')->join(', ') ?: 'Zatím nepřiřazeno' }}</small></span><span class="row-meta"><span class="status-label">{{ $taskStatuses[$task->status] }}</span><small>{{ $task->due_at?->format('j. n. Y') ?? 'Bez termínu' }}</small></span></a>
  @empty
  <div class="empty-work"><h3>Co je potřeba udělat?</h3><p>Rozdělte projekt na konkrétní úkoly a přiřaďte je sobě nebo kolegovi.</p><a href="{{ route('tasks.create', ['project_id' => $project->id]) }}" class="btn btn-primary">Přidat první úkol</a></div>
  @endforelse
</section>
@elseif($tab === 'notes')
<div class="notes-layout">
  <section class="card work-panel note-composer"><header class="panel-heading"><div><h2>Zapsat domluvu</h2><p>Rozhodnutí, podklady nebo další postup</p></div></header><form class="p-4" method="POST" action="{{ route('projects.notes.store', $project) }}">@csrf<label for="project-note" class="form-label">Poznámka pro vás oba</label><textarea id="project-note" name="body" rows="6" maxlength="10000" required class="form-control" placeholder="Co potřebuje kolega vědět?">{{ old('body') }}</textarea><button class="btn btn-primary mt-3" type="submit">Přidat poznámku</button></form></section>
  <section aria-label="Projektové poznámky">
    @forelse($notes as $note)
    <article class="card work-panel mb-3"><header class="panel-heading"><div class="d-flex align-items-center gap-2"><span class="person-avatar">{{ mb_substr($note->user?->name ?? 'U', 0, 1) }}</span><div><h2 class="text-sm">{{ $note->user?->name ?? 'Odstraněný uživatel' }}</h2><p>{{ $note->created_at->copy()->timezone(config('app.display_timezone'))->format('j. n. Y H:i') }}</p></div></div>@if(auth()->user()->isAdmin() || $note->user_id === auth()->id())<form method="POST" action="{{ route('projects.notes.destroy', [$project, $note]) }}" onsubmit="return confirm('Odstranit tuto poznámku?')">@csrf @method('DELETE')<button class="btn btn-link text-secondary m-0 p-0" type="submit">Odstranit</button></form>@endif</header><div class="p-4 note-body">{{ $note->body }}</div></article>
    @empty
    <div class="card empty-work"><h3>Všechny domluvy na jednom místě</h3><p>Zapište první poznámku. U každé zůstane autor a datum.</p></div>
    @endforelse
    {{ $notes->links() }}
  </section>
</div>
@elseif($tab === 'time')
<section class="card work-panel"><header class="panel-heading"><div><h2>Čas na projektu</h2><p>Celkem {{ intdiv($totalMinutes, 60) }} h {{ $totalMinutes % 60 }} min v dokončených záznamech</p></div></header>
  @forelse($entries->sortByDesc('started_at') as $entry)
  <a class="work-row" href="{{ route('tasks.show', $entry->task_id) }}"><span class="row-content"><strong>{{ $project->tasks->firstWhere('id', $entry->task_id)?->title }}</strong><small>{{ $entry->createdBy?->name }} · {{ $entry->started_at->copy()->timezone(config('app.display_timezone'))->format('j. n. Y H:i') }}@if($entry->notes) · {{ $entry->notes }}@endif</small></span><strong>{{ $entry->duration_formatted }}</strong></a>
  @empty
  <div class="empty-work"><h3>Čas se zaznamenává u úkolů</h3><p>Otevřete úkol a spusťte časovač nebo přidejte čas ručně.</p><a href="{{ route('projects.show', $project) }}">Přejít na úkoly</a></div>
  @endforelse
</section>
@else
<div class="work-grid">
  <section class="card work-panel"><header class="panel-heading"><h2>O projektu</h2></header><div class="p-4"><p class="note-body">{{ $project->description ?: 'Popis projektu zatím není vyplněný.' }}</p>@if($project->web_url)<a href="{{ $project->web_url }}" target="_blank" rel="noopener noreferrer">{{ $project->web_url }}</a>@endif<h3 class="mt-4">Lidé na projektu</h3>@foreach($project->users as $member)<div class="d-flex align-items-center gap-2 my-3"><span class="person-avatar">{{ mb_substr($member->name,0,1) }}</span><span>{{ $member->name }}</span>@can('manageMembers', $project)<form method="POST" action="{{ route('projects.detach-user', [$project, $member]) }}" onsubmit="return confirm('Odebrat přístup k projektu?')" class="ms-auto">@csrf @method('DELETE')<button class="btn btn-link text-secondary p-0 m-0">Odebrat</button></form>@endcan</div>@endforeach
  @can('manageMembers', $project)<form class="d-flex gap-2 mt-3" method="POST" action="{{ route('projects.attach-user', $project) }}">@csrf<label for="project-member" class="visually-hidden">Přidat člena projektu</label><select class="form-select" id="project-member" name="user_id" required><option value="">Vyberte kolegu</option>@foreach($allUsers->whereNotIn('id', $project->users->modelKeys()) as $user)<option value="{{ $user->id }}">{{ $user->name }}</option>@endforeach</select><button class="btn btn-outline-primary m-0">Přidat</button></form>@endcan
  </div></section>
  <section class="card work-panel"><header class="panel-heading"><div><h2>Služby projektu</h2><p>Období a úhrady evidované u klienta</p></div><a href="{{ route('clients.show', $project->client) }}">Detail klienta</a></header>@forelse($project->supportPlans as $service)<a href="{{ route('support-plans.show', $service) }}" class="work-row"><span class="row-content"><strong>{{ $service->title }}</strong><small>{{ $service->period_from->format('j. n. Y') }} – {{ $service->period_to->format('j. n. Y') }}</small></span><span class="status-label">{{ $service->paid_at ? 'Uhrazeno' : 'Úhrada neevidována' }}</span></a>@empty<div class="empty-work compact"><p>K tomuto projektu zatím není přiřazena služba. Všechny služby a faktury najdete u klienta.</p></div>@endforelse<a class="panel-footer-link" href="{{ route('invoices.index', ['client_id' => $project->client_id]) }}">Faktury klienta {{ $project->client->name }}</a></section>
</div>
@endif
@endsection
