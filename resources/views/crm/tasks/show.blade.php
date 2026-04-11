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
        <div class="btn-group btn-group-sm" role="group">
          @can('update', $task)
            <a href="{{ route('tasks.edit', $task) }}" class="btn btn-outline-secondary" title="Upravit">
              <i class="fas fa-edit"></i>
            </a>
          @endcan
        </div>
      </div>
      <div class="card-body pt-2">
        <h6 class="font-weight-bold d-flex align-items-center justify-content-between">
          {{ $task->title }}
          <a href="{{ route('tasks.show', $task) }}" class="btn btn-link text-primary p-0" title="Otevřít na celé stránce">
            <i class="fas fa-external-link-alt fa-xs"></i>
          </a>
        </h6>
        @if($task->description)
          <p class="text-sm text-secondary">{{ $task->description }}</p>
        @endif
        <ul class="list-group list-group-flush mt-3">
          <li class="list-group-item ps-0 border-0 d-flex justify-content-between align-items-center">
            <small class="text-secondary">
              <i class="fas fa-circle-notch fa-sm me-1 opacity-5"></i>Stav
            </small>
            <span class="badge bg-gradient-{{ $sc[$task->status] }}">{{ ['todo'=>'K řešení','in_progress'=>'Probíhá','review'=>'Ke kontrole','done'=>'Dokončeno'][$task->status] ?? $task->status }}</span>
          </li>
          <li class="list-group-item ps-0 border-0 d-flex justify-content-between align-items-center">
            <small class="text-secondary">
              <i class="fas fa-flag fa-sm me-1 opacity-5"></i>Priorita
            </small>
            <span class="badge bg-gradient-{{ $pc[$task->priority] }}">{{ ['low'=>'Nízká','medium'=>'Střední','high'=>'Vysoká'][$task->priority] ?? $task->priority }}</span>
          </li>
          <li class="list-group-item ps-0 border-0 d-flex justify-content-between align-items-center">
            <small class="text-secondary">
              <i class="fas fa-folder fa-sm me-1 opacity-5"></i>Projekt
            </small>
            <a href="{{ route('projects.show', $task->project) }}" class="text-sm">{{ $task->project->name }}</a>
          </li>
          <li class="list-group-item ps-0 border-0 d-flex justify-content-between align-items-center">
            <small class="text-secondary">
              <i class="fas fa-building fa-sm me-1 opacity-5"></i>Klient
            </small>
            <a href="{{ route('clients.show', $task->project->client) }}" class="text-sm">{{ $task->project->client->name }}</a>
          </li>
          @if($task->due_date)
          <li class="list-group-item ps-0 border-0 d-flex justify-content-between align-items-center">
            <small class="text-secondary">
              <i class="fas fa-calendar fa-sm me-1 opacity-5"></i>Termín
            </small>
            <span class="text-sm {{ $task->due_date->isPast() && $task->status !== 'done' ? 'text-danger' : '' }}">
              {{ $task->due_date->format('d.m.Y') }}
            </span>
          </li>
          @endif
        </ul>

        {{-- Assignees --}}
        @if($task->assignees->count())
        <div class="mt-3 pt-2 border-top">
          <p class="text-xs text-secondary mb-2">
            <i class="fas fa-users fa-sm opacity-5 me-1"></i>PŘIŘAZENÍ
          </p>
          <div class="d-flex flex-wrap gap-1">
            @foreach($task->assignees as $assignee)
              <span class="badge bg-gradient-light text-dark">{{ $assignee->name }}</span>
            @endforeach
          </div>
        </div>
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

    {{-- Time tracking --}}
    @include('crm.tasks._time_entries', $task)
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

@include('crm.tasks._time_entry_modal', $task)
@endsection

