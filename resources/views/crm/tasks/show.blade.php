@extends('layouts.user_type.auth')

@section('content')
@php
  $sc = ['todo'=>'secondary','in_progress'=>'primary','review'=>'warning','done'=>'success'];
  $pc = ['low'=>'info','medium'=>'warning','high'=>'danger'];
@endphp

<div class="row">
  {{-- Main content: Title + Description + Comments --}}
  <div class="col-lg-8">
    {{-- Title & Description Card --}}
    <div class="card mb-4">
      <div class="card-header pb-0 pt-4 border-0">
        <div class="d-flex justify-content-between align-items-start">
          <div style="flex: 1;">
            <h4 class="mb-3">{{ $task->title }}</h4>
            <div class="d-flex gap-3 mb-3">
              <span class="badge bg-gradient-{{ $sc[$task->status] ?? 'secondary' }}">
                {{ ['todo'=>'K řešení','in_progress'=>'Probíhá','review'=>'Ke kontrole','done'=>'Dokončeno'][$task->status] ?? $task->status }}
              </span>
              <span class="badge bg-gradient-{{ $pc[$task->priority] ?? 'secondary' }}">
                {{ ['low'=>'Nízká','medium'=>'Střední','high'=>'Vysoká'][$task->priority] ?? $task->priority }}
              </span>
              @if($task->due_date)
                <small class="text-secondary align-self-center">
                  <i class="fas fa-calendar fa-xs me-1"></i>{{ $task->due_date->format('d.m.Y') }}
                </small>
              @endif
            </div>
          </div>
          <div class="btn-group btn-group-sm" role="group">
            @can('update', $task)
              <a href="{{ route('tasks.edit', $task) }}" class="btn btn-outline-secondary" title="Upravit">
                <i class="fas fa-edit"></i>
              </a>
            @endcan
            @can('delete', $task)
              <form method="POST" action="{{ route('tasks.destroy', $task) }}" class="d-inline" onsubmit="return confirm('Smazat úkol?')">
                @csrf @method('DELETE')
                <button class="btn btn-outline-danger" title="Smazat" type="submit">
                  <i class="fas fa-trash"></i>
                </button>
              </form>
            @endcan
          </div>
        </div>
      </div>
      @if($task->description)
      <div class="card-body pt-2">
        <div class="p-3 bg-light rounded" style="white-space: pre-wrap;">
          {{ $task->description }}
        </div>
      </div>
      @endif
    </div>

    {{-- Comments --}}
    <div class="card mb-4">
      <div class="card-header pb-0">
        <h6 class="mb-0">
          <i class="fas fa-comments me-2 text-primary opacity-75"></i>Komentáře ({{ $task->allComments->count() }})
        </h6>
        <small class="text-secondary">Použijte @uživatelské_jméno pro zmínku</small>
      </div>
      <div class="card-body">

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

  {{-- Sidebar: Assignees + Time Tracking --}}
  <div class="col-lg-4">
    {{-- Assignees --}}
    <div class="card mb-3">
      <div class="card-header pb-3 pt-3">
        <h6 class="mb-0 text-sm">
          <i class="fas fa-user me-2 text-primary opacity-75"></i>Přiřazeno
        </h6>
      </div>
      <div class="card-body pt-2 pb-2">
        @if($task->assignees->count())
          @foreach($task->assignees as $assignee)
            <span class="badge bg-light text-dark d-block mb-2">{{ $assignee->name }}</span>
          @endforeach
        @else
          <small class="text-secondary">Nikdo</small>
        @endif
      </div>
    </div>

    {{-- Project & Client --}}
    <div class="card mb-3">
      <div class="card-body p-3">
        <div class="mb-3">
          <small class="text-secondary d-block mb-1 fw-bold">PROJEKT</small>
          <a href="{{ route('projects.show', $task->project) }}" class="text-xs">
            {{ $task->project->name }} <i class="fas fa-external-link-alt fa-xs ms-1"></i>
          </a>
        </div>
        <div class="mb-0">
          <small class="text-secondary d-block mb-1 fw-bold">KLIENT</small>
          <a href="{{ route('clients.show', $task->project->client) }}" class="text-xs">
            {{ $task->project->client->name }} <i class="fas fa-external-link-alt fa-xs ms-1"></i>
          </a>
        </div>
      </div>
    </div>

    {{-- Full Time Tracking Component --}}
    @include('crm.tasks._time_entries', ['task' => $task])
  </div>
</div>

@include('crm.tasks._time_entry_modal', $task)
@endsection


