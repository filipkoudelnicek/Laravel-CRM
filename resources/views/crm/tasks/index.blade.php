@extends('layouts.user_type.auth')
@section('title', 'Úkoly')
@section('content')
<x-page-heading title="Úkoly" description="Další kroky napříč projekty."><a href="{{ route('tasks.create') }}" class="btn btn-primary"><x-icon name="plus" />Nový úkol</a></x-page-heading>
<nav class="work-tabs" aria-label="Pohledy úkolů">
@foreach(['all'=>'Všechny úkoly','open'=>'Otevřené','mine'=>'Moje úkoly','overdue'=>'Po termínu'] as $key=>$label)
<a href="{{ route('tasks.index', array_merge(request()->except('view','status','mine','page'), ['view'=>$key])) }}" class="{{ request('view','all') === $key ? 'active' : '' }}" @if(request('view','all') === $key) aria-current="page" @endif>{{ $label }}</a>
@endforeach
</nav>
<section class="list-panel">
  <form method="GET" class="list-toolbar">
    <input type="hidden" name="view" value="{{ request('view','all') }}">
    @if(request('project_id'))<input type="hidden" name="project_id" value="{{ request('project_id') }}">@endif
    @if(request('mine'))<input type="hidden" name="mine" value="1">@endif
    <x-search-field placeholder="Hledat úkol…" />
    <select name="status" class="form-select" aria-label="Filtrovat podle stavu"><option value="">Všechny stavy</option>@foreach(['todo'=>'K řešení','in_progress'=>'Probíhá','review'=>'Ke kontrole','done'=>'Dokončeno'] as $key=>$label)<option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>@endforeach</select>
    <select name="priority" class="form-select" aria-label="Filtrovat podle priority"><option value="">Všechny priority</option>@foreach(['low'=>'Nízká','medium'=>'Střední','high'=>'Vysoká'] as $key=>$label)<option value="{{ $key }}" @selected(request('priority') === $key)>{{ $label }}</option>@endforeach</select>
    <select name="sort" class="form-select" aria-label="Řazení úkolů"><option value="newest">Nejnovější</option><option value="due" @selected(request('sort') === 'due')>Podle termínu</option><option value="title" @selected(request('sort') === 'title')>Podle názvu</option></select>
    <button class="btn btn-outline-secondary">Použít</button>
    @if(request()->hasAny(['q','status','priority','project_id','mine']))<a href="{{ route('tasks.index',['view'=>request('view','all')]) }}" class="btn btn-link">Zrušit filtry</a>@endif
  </form>
  @include('crm.tasks._table', ['tasks'=>$tasks])
  <x-list-footer :items="$tasks" />
</section>
@endsection
