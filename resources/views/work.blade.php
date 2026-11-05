@extends('layouts.user_type.auth')
@section('title', 'Moje práce')
@section('content')
<div class="workspace-heading">
  <div><h1>Moje práce</h1><p class="page-date">{{ now(config('app.display_timezone'))->locale('cs')->translatedFormat('l j. F Y') }}</p></div>
  <a href="{{ route('tasks.create') }}" class="btn btn-primary"><x-icon name="plus" />Nový úkol</a>
</div>
<div class="overview-stats" aria-label="Souhrn mé práce">
  <a href="{{ route('tasks.index',['view'=>'mine']) }}"><strong>{{ $myTaskCount }}</strong> otevřených úkolů</a>
  <a href="{{ route('projects.index',['status'=>'active']) }}"><strong>{{ $activeProjects }}</strong> aktivní projekty</a>
  <span><strong>{{ intdiv($weekMinutes,60) }} h {{ $weekMinutes%60 }} min</strong> tento týden</span>
</div>
<div class="overview-grid work-overview">
  <section class="section-panel panel-work">
    <header class="section-heading"><h2>Moje úkoly</h2><a href="{{ route('tasks.index',['view'=>'mine','sort'=>'due']) }}">Všechny moje úkoly</a></header>
    @php
      $late = $myTasks->filter(fn($task) => $task->due_at && $task->due_at->toDateString() < now(config('app.display_timezone'))->toDateString());
      $next = $myTasks->reject(fn($task) => $late->contains($task));
    @endphp
    @if($late->isNotEmpty())
      <div class="overview-section-label is-overdue"><x-icon name="clock" />Po termínu <span>{{ $overdueCount }}</span></div>
      <div class="task-surface">@include('crm.tasks._table',['tasks'=>$late,'compact'=>true])</div>
    @endif
    @if($next->isNotEmpty() || $late->isEmpty())
      <div class="overview-section-label">Dále na řadě</div>
      <div class="task-surface">@include('crm.tasks._table',['tasks'=>$next,'compact'=>true])</div>
    @endif
  </section>
  <aside class="overview-context">
    <section class="quiet-section section-panel panel-services">
      <header class="section-heading"><h2>Končící služby</h2><a href="{{ route('support-plans.index',['expiring'=>1]) }}">{{ $expiringCount }} celkem</a></header>
      @forelse($expiringServices as $service)
        <a class="context-item" href="{{ route('support-plans.show',$service) }}"><strong>{{ $service->title }}</strong><small>{{ $service->client->name }}</small><div class="context-item-footer"><span>Do {{ $service->period_to->format('j. n. Y') }}</span><span @class(['text-success'=>$service->paid_at])>{{ $service->paid_at ? 'Uhrazeno' : 'Úhrada neevidována' }}</span></div></a>
      @empty
        <div class="empty-work compact"><p>V příštích 30 dnech nekončí žádná aktivní služba.</p><a href="{{ route('support-plans.index') }}">Přehled služeb</a></div>
      @endforelse
    </section>
    <section class="quiet-section section-panel panel-notes">
      <header class="section-heading"><h2>Poslední domluvy</h2></header>
      @forelse($recentNotes->take(3) as $note)
        <a class="context-item" href="{{ route('projects.show',[$note->project,'tab'=>'notes']) }}"><strong>{{ $note->project->name }}</strong><p>{{ Str::limit($note->body,125) }}</p><div class="context-item-footer"><span>{{ $note->user?->name ?? 'Odstraněný uživatel' }}</span><span>{{ $note->created_at->diffForHumans() }}</span></div></a>
      @empty
        <div class="empty-work compact"><p>Domluvy s kolegou zapisujte do poznámek u projektu.</p><a href="{{ route('projects.index') }}">Otevřít projekty</a></div>
      @endforelse
    </section>
  </aside>
</div>
@endsection
