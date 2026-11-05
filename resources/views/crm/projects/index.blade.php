@extends('layouts.user_type.auth')
@section('title', 'Projekty')
@section('content')
@php $statuses=['planned'=>'Plánované','active'=>'Aktivní','on_hold'=>'Pozastavené','done'=>'Dokončené']; $singleStatuses=['planned'=>'Plánovaný','active'=>'Aktivní','on_hold'=>'Pozastavený','done'=>'Dokončený']; @endphp
<x-page-heading title="Projekty">@can('create', \App\Models\Project::class)<a href="{{ route('projects.create') }}" class="btn btn-primary"><x-icon name="plus" />Nový projekt</a>@endcan</x-page-heading>
<nav class="work-tabs" aria-label="Stavy projektů">
  @foreach([''=>'Všechny projekty']+$statuses as $key=>$label)
    <a href="{{ route('projects.index',array_merge(request()->except('status','page'),['status'=>$key])) }}" class="{{ request('status','') === $key ? 'active' : '' }}" @if(request('status','') === $key) aria-current="page" @endif>{{ $label }}</a>
  @endforeach
</nav>
<form method="GET" class="filter-form project-filters">
  <div class="filter-bar"><x-search-field placeholder="Najít projekt…" />@if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
    <select name="sort" class="form-select" aria-label="Řazení projektů" style="width:175px"><option value="newest">Nejnovější projekty</option><option value="due" @selected(request('sort') === 'due')>Podle termínu</option><option value="name" @selected(request('sort') === 'name')>Podle názvu</option></select>
    <button class="btn btn-outline-secondary">Použít</button>@if(request('q'))<a href="{{ route('projects.index',request()->only('status','sort')) }}" class="btn btn-link">Zrušit hledání</a>@endif
  </div>
</form>
<section class="project-grid" aria-label="Přehled projektů">
  @forelse($projects as $project)
    @php $progress=$project->tasks_count ? round($project->done_tasks_count/$project->tasks_count*100) : 0; @endphp
    <article class="project-card">
      <div class="project-card-top"><span class="project-symbol"><x-icon name="folder" /></span><x-record-actions :record="$project" resource="projects" :label="$project->name" /></div>
      <h2><a href="{{ route('projects.show',$project) }}">{{ $project->name }}</a></h2>
      <span class="project-client">{{ $project->client->name }}</span>
      <div class="project-card-meta"><span class="status-chip" data-status="{{ $project->status }}">{{ $singleStatuses[$project->status] }}</span><span>{{ $project->due_date?->format('j. n. Y') ?? 'Bez termínu' }}</span></div>
      <div class="project-card-progress"><div class="progress-inline"><span>{{ $project->done_tasks_count }} z {{ $project->tasks_count }} úkolů hotovo</span><span class="numeric">{{ $progress }} %</span></div><span class="progress-track" aria-hidden="true"><span style="width:{{ $progress }}%"></span></span></div>
      <div class="project-card-bottom"><div class="assignee-stack">@forelse($project->users->take(3) as $person)<span class="person-avatar" title="{{ $person->name }}" aria-label="{{ $person->name }}">{{ mb_substr($person->name,0,1) }}</span>@empty<span>Bez přiřazených lidí</span>@endforelse @if($project->users->count()>3)<span>+{{ $project->users->count()-3 }}</span>@endif</div><span>Otevřít projekt <x-icon name="chevron" style="width:13px;height:13px" /></span></div>
    </article>
  @empty
    <div class="empty-work" style="grid-column:1/-1"><h3>Žádné odpovídající projekty</h3><p>Vyberte jiný stav nebo založte první zakázku.</p>@can('create',\App\Models\Project::class)<a class="btn btn-outline-secondary" href="{{ route('projects.create') }}">Vytvořit projekt</a>@endcan</div>
  @endforelse
</section>
<footer class="project-collection-footer"><span>{{ $projects->total() }} projektů</span>{{ $projects->links() }}</footer>
@endsection
