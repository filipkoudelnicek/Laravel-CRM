@extends('layouts.user_type.auth')
@section('title', 'Moje práce')
@section('content')
<div class="workspace-heading">
  <div><p class="context-line">{{ now(config('app.display_timezone'))->locale('cs')->translatedFormat('l j. F Y') }}</p><h1>Co je dnes na řadě</h1><p class="text-secondary mb-0">Úkoly, domluvy a služby, které potřebují vaši pozornost.</p></div>
  <a href="{{ route('tasks.create') }}" class="btn btn-primary">+ Nový úkol</a>
</div>
<div class="workspace-metrics">
  <a href="{{ route('tasks.index', ['mine' => 1]) }}"><span>Moje otevřené úkoly</span><strong>{{ $myTaskCount }}</strong><small>{{ $overdueCount ? $overdueCount . ' po termínu' : 'Žádný po termínu' }}</small></a>
  <a href="{{ route('projects.index', ['status' => 'active']) }}"><span>Aktivní projekty</span><strong>{{ $activeProjects }}</strong><small>Rozpracované zakázky</small></a>
  <div><span>Můj čas tento týden</span><strong>{{ intdiv($weekMinutes, 60) }}<em>h</em> {{ $weekMinutes % 60 }}<em>m</em></strong><small>Uložené časové záznamy</small></div>
  <a href="{{ route('support-plans.index', ['expiring' => 1]) }}"><span>Končící služby</span><strong class="{{ $expiringCount ? 'text-warning' : '' }}">{{ $expiringCount }}</strong><small>V následujících 30 dnech</small></a>
</div>
<div class="work-grid">
  <section class="card work-panel">
    <header class="panel-heading"><div><h2>Moje úkoly</h2><p>Nejbližší termíny jako první</p></div><a href="{{ route('tasks.index', ['mine' => 1]) }}">Zobrazit všechny</a></header>
    @forelse($myTasks as $task)
      <a class="work-row" href="{{ route('tasks.show', $task) }}">
        <span class="task-indicator status-{{ $task->status }}" aria-hidden="true"></span>
        <span class="row-content"><strong>{{ $task->title }}</strong><small>{{ $task->project->client->name }} / {{ $task->project->name }}</small></span>
        <span class="row-meta"><span class="status-label">{{ ['todo'=>'K řešení','in_progress'=>'Probíhá','review'=>'Ke kontrole'][$task->status] ?? $task->status }}</span><small class="{{ $task->due_at && $task->due_at->toDateString() < now(config('app.display_timezone'))->toDateString() ? 'text-danger' : '' }}">{{ $task->due_at?->format('j. n.') ?? 'Bez termínu' }}</small></span>
      </a>
    @empty
      <div class="empty-work"><h3>Prostor pro další krok</h3><p>Nemáte přiřazený žádný otevřený úkol.</p><a href="{{ route('tasks.create') }}" class="btn btn-outline-primary">Vytvořit úkol</a></div>
    @endforelse
  </section>
  <section class="card work-panel">
    <header class="panel-heading"><div><h2>Pohlídat prodloužení</h2><p>Služby, kterým končí období</p></div><a href="{{ route('support-plans.index') }}">Všechny služby</a></header>
    @forelse($expiringServices as $service)
      <a class="work-row service-row" href="{{ route('support-plans.show', $service) }}">
        <span class="row-content"><strong>{{ $service->title }}</strong><small>{{ $service->client->name }}</small></span>
        <span class="row-meta"><strong>{{ $service->period_to->format('j. n. Y') }}</strong><small>{{ $service->paid_at ? 'Úhrada evidována' : 'Úhrada neevidována' }}</small></span>
      </a>
    @empty
      <div class="empty-work"><h3>Termíny jsou v pořádku</h3><p>V příštích 30 dnech nekončí žádná aktivní služba.</p><a href="{{ route('support-plans.index') }}">Přejít na přehled služeb</a></div>
    @endforelse
  </section>
</div>
<section class="card work-panel mt-4">
  <header class="panel-heading"><div><h2>Poslední domluvy u projektů</h2><p>Poznámky vás i kolegy na jednom místě</p></div></header>
  @forelse($recentNotes as $note)
    <a class="work-row" href="{{ route('projects.show', [$note->project, 'tab' => 'notes']) }}">
      <span class="person-avatar">{{ mb_substr($note->user?->name ?? 'U', 0, 1) }}</span><span class="row-content"><strong>{{ Str::limit($note->body, 130) }}</strong><small>{{ $note->user?->name ?? 'Odstraněný uživatel' }} · {{ $note->project->name }}</small></span><span class="row-meta"><small>{{ $note->created_at->diffForHumans() }}</small></span>
    </a>
  @empty
    <div class="empty-work compact"><p>Přidejte první poznámku na detailu projektu. Oba pak uvidíte, na čem jste se domluvili.</p><a href="{{ route('projects.index') }}">Otevřít projekty</a></div>
  @endforelse
</section>
@endsection
