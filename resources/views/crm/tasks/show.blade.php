@extends('layouts.user_type.auth')

@section('content')
@php
  $sc = ['todo'=>'secondary','in_progress'=>'primary','review'=>'warning','done'=>'success'];
  $pc = ['low'=>'info','medium'=>'warning','high'=>'danger'];
@endphp

{{-- Header --}}
<div class="row mb-4">
  <div class="col-12">
    <div class="d-flex justify-content-between align-items-start">
      <div>
        <h3 class="mb-1">
          <i class="fas fa-tasks me-2 text-primary opacity-75"></i>{{ $task->title }}
        </h3>
        @if($task->description)
          <p class="text-secondary mb-0">{{ $task->description }}</p>
        @endif
      </div>
      <div class="btn-group btn-group-sm" role="group">
        @can('update', $task)
          <a href="{{ route('tasks.edit', $task) }}" class="btn btn-outline-secondary">
            <i class="fas fa-edit me-1"></i> Upravit
          </a>
        @endcan
        @can('delete', $task)
          <form method="POST" action="{{ route('tasks.destroy', $task) }}" class="d-inline" onsubmit="return confirm('Smazat úkol?')">
            @csrf @method('DELETE')
            <button class="btn btn-outline-danger">
              <i class="fas fa-trash me-1"></i> Smazat
            </button>
          </form>
        @endcan
      </div>
    </div>
  </div>
</div>

<div class="row">
  {{-- Task details --}}
  <div class="col-lg-4">
    {{-- Info card --}}
    <div class="card mb-4">
      <div class="card-header pb-3 pt-4">
        <h6 class="mb-0">
          <i class="fas fa-info-circle me-2 text-primary opacity-75"></i>Informace
        </h6>
      </div>
      <div class="card-body">
        {{-- Status & Priority --}}
        <div class="row g-3 mb-4">
          <div class="col-6">
            <small class="text-secondary d-block mb-2">
              <i class="fas fa-circle-notch fa-xs me-1 opacity-75"></i>STAV
            </small>
            <span class="badge bg-gradient-{{ $sc[$task->status] }} px-3 py-2">
              {{ ['todo'=>'K řešení','in_progress'=>'Probíhá','review'=>'Ke kontrole','done'=>'Dokončeno'][$task->status] ?? $task->status }}
            </span>
          </div>
          <div class="col-6">
            <small class="text-secondary d-block mb-2">
              <i class="fas fa-flag fa-xs me-1 opacity-75"></i>PRIORITA
            </small>
            <span class="badge bg-gradient-{{ $pc[$task->priority] }} px-3 py-2">
              {{ ['low'=>'Nízká','medium'=>'Střední','high'=>'Vysoká'][$task->priority] ?? $task->priority }}
            </span>
          </div>
        </div>

        <hr class="my-3">

        {{-- Projekt & Client --}}
        <div class="mb-4">
          <small class="text-secondary d-block mb-2">
            <i class="fas fa-folder fa-xs me-1 opacity-75"></i>PROJEKT
          </small>
          <a href="{{ route('projects.show', $task->project) }}" class="text-sm fw-500">
            {{ $task->project->name }} <i class="fas fa-external-link-alt fa-xs ms-1 opacity-75"></i>
          </a>
        </div>

        <div class="mb-4">
          <small class="text-secondary d-block mb-2">
            <i class="fas fa-building fa-xs me-1 opacity-75"></i>KLIENT
          </small>
          <a href="{{ route('clients.show', $task->project->client) }}" class="text-sm fw-500">
            {{ $task->project->client->name }} <i class="fas fa-external-link-alt fa-xs ms-1 opacity-75"></i>
          </a>
        </div>

        @if($task->due_date)
        <div class="mb-4">
          <small class="text-secondary d-block mb-2">
            <i class="fas fa-calendar fa-xs me-1 opacity-75"></i>TERMÍN
          </small>
          <span class="text-sm {{ $task->due_date->isPast() && $task->status !== 'done' ? 'text-danger fw-bold' : '' }}">
            {{ $task->due_date->format('d.m.Y') }}
          </span>
        </div>
        @endif

        {{-- Assignees --}}
        @if($task->assignees->count())
        <hr class="my-3">
        <small class="text-secondary d-block mb-2">
          <i class="fas fa-users fa-xs me-1 opacity-75"></i>PŘIŘAZENÍ ({{ $task->assignees->count() }})
        </small>
        <div class="d-flex flex-wrap gap-1">
          @foreach($task->assignees as $assignee)
            <span class="badge bg-light text-dark">{{ $assignee->name }}</span>
          @endforeach
        </div>
        @endif
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

