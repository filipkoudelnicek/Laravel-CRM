@extends('layouts.user_type.auth')

@section('content')
@php
  $sc = ['todo'=>'secondary','in_progress'=>'primary','review'=>'warning','done'=>'success'];
  $pc = ['low'=>'info','medium'=>'warning','high'=>'danger'];
@endphp

<div class="row">
  {{-- Task details --}}
  <div class="col-lg-4">
    <div class="card mb-4">
      <div class="card-header pb-0 d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Úkol</h5>
        @can('update', $task)
          <a href="{{ route('tasks.edit', $task) }}" class="btn btn-outline-secondary btn-sm">Upravit</a>
        @endcan
      </div>
      <div class="card-body pt-2">
        <h6 class="font-weight-bold">{{ $task->title }}</h6>
        @if($task->description)
          <p class="text-sm text-secondary">{{ $task->description }}</p>
        @endif
        <ul class="list-group list-group-flush mt-2">
          <li class="list-group-item ps-0 border-0 d-flex justify-content-between">
            <small class="text-secondary">Stav</small>
            <span class="badge bg-gradient-{{ $sc[$task->status] }}">{{ ['todo'=>'K řešení','in_progress'=>'Probíhá','review'=>'Ke kontrole','done'=>'Dokončeno'][$task->status] ?? $task->status }}</span>
          </li>
          <li class="list-group-item ps-0 border-0 d-flex justify-content-between">
            <small class="text-secondary">Priorita</small>
            <span class="badge bg-gradient-{{ $pc[$task->priority] }}">{{ ['low'=>'Nízká','medium'=>'Střední','high'=>'Vysoká'][$task->priority] ?? $task->priority }}</span>
          </li>
          <li class="list-group-item ps-0 border-0 d-flex justify-content-between">
            <small class="text-secondary">Projekt</small>
            <a href="{{ route('projects.show', $task->project) }}" class="text-sm">{{ $task->project->name }}</a>
          </li>
          <li class="list-group-item ps-0 border-0 d-flex justify-content-between">
            <small class="text-secondary">Klient</small>
            <a href="{{ route('clients.show', $task->project->client) }}" class="text-sm">{{ $task->project->client->name }}</a>
          </li>
          @if($task->due_date)
          <li class="list-group-item ps-0 border-0 d-flex justify-content-between">
            <small class="text-secondary">Termín</small>
            <span class="text-sm {{ $task->due_date->isPast() && $task->status !== 'done' ? 'text-danger' : '' }}">
              {{ $task->due_date->format('d.m.Y') }}
            </span>
          </li>
          @endif
        </ul>

        {{-- Assignees --}}
        @if($task->assignees->count())
        <p class="text-xs text-secondary mt-3 mb-1 text-uppercase font-weight-bolder">Přiřazení</p>
        @foreach($task->assignees as $assignee)
          <span class="badge bg-gradient-light text-dark me-1">{{ $assignee->name }}</span>
        @endforeach
        @endif

        @can('delete', $task)
          <hr>
          <form method="POST" action="{{ route('tasks.destroy', $task) }}" onsubmit="return confirm('Smazat úkol?')">
            @csrf @method('DELETE')
            <button class="btn btn-danger btn-sm w-100">Smazat úkol</button>
          </form>
        @endcan
      </div>
    </div>
  </div>

  {{-- Comments --}}
  <div class="col-lg-8">
    <div class="card mb-4">
      <div class="card-header pb-0">
        <h6 class="mb-0">Komentáře ({{ $task->allComments->count() }})</h6>
        <small class="text-secondary">Použijte @uživatelské_jméno pro zmínku</small>
      </div>
      <div class="card-body">

        @if(session('success'))
          <div class="alert alert-success py-2">{{ session('success') }}</div>
        @endif

        {{-- New top-level comment --}}
        <form method="POST" action="{{ route('tasks.comments.store', $task) }}" class="mb-4">
          @csrf
          <div class="mb-2">
            <textarea name="body" rows="3" class="form-control" placeholder="Napište komentář… použijte @jméno pro zmínku"
              required>{{ old('body') }}</textarea>
            @error('body') <div class="text-danger text-xs mt-1">{{ $message }}</div> @enderror
          </div>
          <button class="btn bg-gradient-primary btn-sm">Přidat komentář</button>
        </form>

        {{-- Threaded comments --}}
        @forelse($task->comments as $comment)
          @include('crm.tasks._comment', ['comment' => $comment, 'depth' => 0])
        @empty
          <p class="text-sm text-secondary">Žádné komentáře.</p>
        @endforelse

      </div>
    </div>
  </div>
</div>
@endsection

