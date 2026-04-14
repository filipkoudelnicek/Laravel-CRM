@props(['task'])

@php
  $sc = ['todo'=>'secondary','in_progress'=>'primary','review'=>'warning','done'=>'success'];
  $pc = ['low'=>'info','medium'=>'warning','high'=>'danger'];
@endphp

<div class="modal-header border-0 bg-transparent pb-2">
  <h5 class="modal-title fs-6 fw-bold">{{ $task->title }}</h5>
  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body pb-0">
  {{-- Description --}}
  @if($task->description)
    <div class="mb-4 p-3 bg-light rounded">
      <div class="text-sm mb-0 rich-content">{!! $task->description !!}</div>
    </div>
  @endif

  {{-- Status & Priority --}}
  <div class="row g-2 mb-4">
    <div class="col-6">
      <small class="text-secondary d-block mb-2"><i class="fas fa-circle-notch fa-xs me-1"></i>STAV</small>
      <span class="badge bg-gradient-{{ $sc[$task->status] }} px-3 py-2 w-100 text-center">
        {{ ['todo'=>'K řešení','in_progress'=>'Probíhá','review'=>'Ke kontrole','done'=>'Dokončeno'][$task->status] }}
      </span>
    </div>
    <div class="col-6">
      <small class="text-secondary d-block mb-2"><i class="fas fa-flag fa-xs me-1"></i>PRIORITA</small>
      <span class="badge bg-gradient-{{ $pc[$task->priority] }} px-3 py-2 w-100 text-center">
        {{ ['low'=>'Nízká','medium'=>'Střední','high'=>'Vysoká'][$task->priority] }}
      </span>
    </div>
  </div>

  {{-- Project & Client --}}
  <div class="mb-4">
    <small class="text-secondary d-block mb-2 fw-bold"><i class="fas fa-folder fa-xs me-1"></i>PROJEKT</small>
    <a href="{{ route('projects.show', $task->project) }}" class="text-decoration-none text-dark fw-500">
      {{ $task->project->name }} <i class="fas fa-external-link-alt fa-xs ms-1 opacity-50"></i>
    </a>
  </div>

  <div class="mb-4">
    <small class="text-secondary d-block mb-2 fw-bold"><i class="fas fa-building fa-xs me-1"></i>KLIENT</small>
    <a href="{{ route('clients.show', $task->project->client) }}" class="text-decoration-none text-dark fw-500">
      {{ $task->project->client->name }} <i class="fas fa-external-link-alt fa-xs ms-1 opacity-50"></i>
    </a>
  </div>

  {{-- Date Range --}}
  @if($task->starts_at || $task->due_at)
  <div class="mb-4">
    <div class="row g-2">
      @if($task->starts_at)
      <div class="col-6">
        <small class="text-secondary d-block mb-2 fw-bold"><i class="fas fa-play fa-xs me-1"></i>ZAČÁTEK</small>
        <span class="text-sm">{{ $task->starts_at->format('d.m.Y') }}</span>
      </div>
      @endif
      @if($task->due_at)
      <div class="col-6">
        <small class="text-secondary d-block mb-2 fw-bold"><i class="fas fa-flag-checkered fa-xs me-1"></i>TERMÍN</small>
        <span class="text-sm {{ $task->due_at->isPast() && $task->status !== 'done' ? 'text-danger fw-bold' : '' }}">
          {{ $task->due_at->format('d.m.Y') }}
        </span>
      </div>
      @endif
    </div>
  </div>
  @endif

  {{-- Assignees --}}
  @if($task->assignees->count())
  <div class="mb-4">
    <small class="text-secondary d-block mb-2 fw-bold"><i class="fas fa-users fa-xs me-1"></i>PŘIŘAZENÍ ({{ $task->assignees->count() }})</small>
    <div class="d-flex flex-wrap gap-2">
      @foreach($task->assignees as $assignee)
        <span class="badge bg-info text-white">{{ $assignee->name }}</span>
      @endforeach
    </div>
  </div>
  @endif
</div>

<div class="modal-footer border-top bg-light pt-3">
  <div class="d-flex gap-2 w-100">
    @can('update', $task)
      <a href="{{ route('tasks.edit', $task) }}" class="btn btn-primary btn-sm flex-grow-1">
        <i class="fas fa-edit me-1"></i>Upravit
      </a>
    @endcan
    <a href="{{ route('tasks.show', $task) }}" class="btn btn-outline-primary btn-sm flex-grow-1" target="_blank">
      <i class="fas fa-external-link-alt me-1"></i>Otevřít
    </a>
  </div>
</div>
