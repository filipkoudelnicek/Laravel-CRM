@props(['project'])

@php
  $colors = ['planned'=>'secondary','active'=>'success','on_hold'=>'warning','done'=>'info'];
  $statusLabels = ['planned'=>'Plánovaný','active'=>'Aktivní','on_hold'=>'Pozastavený','done'=>'Dokončený'];
@endphp

<div class="modal-header border-0 bg-transparent pb-2">
  <h5 class="modal-title fs-6 fw-bold">{{ $project->name }}</h5>
  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body pb-0">
  {{-- Description --}}
  @if($project->description)
    <div class="mb-4 p-3 bg-light rounded">
      <p class="text-sm mb-0" style="white-space: pre-wrap;">{{ $project->description }}</p>
    </div>
  @endif

  {{-- Status & Tasks --}}
  <div class="row g-2 mb-4">
    <div class="col-6">
      <small class="text-secondary d-block mb-2"><i class="fas fa-circle-notch fa-xs me-1"></i>STAV</small>
      <span class="badge bg-gradient-{{ $colors[$project->status] }} px-3 py-2 w-100 text-center">
        {{ $statusLabels[$project->status] }}
      </span>
    </div>
    <div class="col-6">
      <small class="text-secondary d-block mb-2"><i class="fas fa-tasks fa-xs me-1"></i>ÚKOLY</small>
      <span class="badge bg-light text-dark px-3 py-2 w-100 text-center">{{ $project->tasks_count ?? 0 }}</span>
    </div>
  </div>

  {{-- Client --}}
  <div class="mb-4">
    <small class="text-secondary d-block mb-2 fw-bold"><i class="fas fa-building fa-xs me-1"></i>KLIENT</small>
    <a href="{{ route('clients.show', $project->client) }}" class="text-decoration-none text-dark fw-500">
      {{ $project->client->name }} <i class="fas fa-external-link-alt fa-xs ms-1 opacity-50"></i>
    </a>
  </div>

  {{-- Due Date --}}
  @if($project->due_date)
  <div class="mb-4">
    <small class="text-secondary d-block mb-2 fw-bold"><i class="fas fa-calendar fa-xs me-1"></i>TERMÍN</small>
    <span class="text-sm">{{ $project->due_date->format('d.m.Y') }}</span>
  </div>
  @endif

  {{-- Creator --}}
  @if($project->creator)
  <div class="mb-4">
    <small class="text-secondary d-block mb-2 fw-bold"><i class="fas fa-user fa-xs me-1"></i>VYTVOŘIL</small>
    <span class="text-sm">{{ $project->creator->name }}</span>
  </div>
  @endif

  @if($project->web_url)
  <div class="mb-4">
    <small class="text-secondary d-block mb-2 fw-bold"><i class="fas fa-globe fa-xs me-1"></i>WEB</small>
    <a href="{{ $project->web_url }}" target="_blank" rel="noopener noreferrer" class="text-decoration-none text-dark fw-500">
      {{ $project->web_url }} <i class="fas fa-external-link-alt fa-xs ms-1 opacity-50"></i>
    </a>
  </div>
  @endif

  {{-- Team --}}
  @if($project->users->count())
  <div class="mb-4">
    <small class="text-secondary d-block mb-2 fw-bold"><i class="fas fa-users fa-xs me-1"></i>TÝM ({{ $project->users->count() }})</small>
    <div class="d-flex flex-wrap gap-2">
      @foreach($project->users as $member)
        <span class="badge bg-warning text-dark" title="{{ $member->pivot->role }}">{{ $member->name }}</span>
      @endforeach
    </div>
  </div>
  @endif
</div>

<div class="modal-footer border-top bg-light pt-3">
  <div class="d-flex gap-2 w-100">
    @can('update', $project)
      <a href="{{ route('projects.edit', $project) }}" class="btn btn-primary btn-sm flex-grow-1">
        <i class="fas fa-edit me-1"></i>Upravit
      </a>
    @endcan
    <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-primary btn-sm flex-grow-1" target="_blank">
      <i class="fas fa-external-link-alt me-1"></i>Otevřít
    </a>
  </div>
</div>
