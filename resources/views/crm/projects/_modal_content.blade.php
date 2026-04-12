@props(['project'])

<div class="modal-header border-bottom bg-transparent">
  <h5 class="modal-title">{{ $project->name }}</h5>
</div>

<div class="modal-body">
  @php
    $colors = ['planned'=>'secondary','active'=>'success','on_hold'=>'warning','done'=>'info'];
    $statusLabels = ['planned'=>'Plánovaný','active'=>'Aktivní','on_hold'=>'Pozastavený','done'=>'Dokončený'];
  @endphp

  @if($project->description)
    <div class="mb-3">
      <h6 class="text-uppercase text-xs text-secondary font-weight-bold mb-2">Popis</h6>
      <p class="text-sm mb-0">{{ $project->description }}</p>
    </div>
  @endif

  <div class="row mb-3">
    <div class="col-6">
      <small class="text-secondary d-block mb-1">Stav</small>
      <span class="badge bg-gradient-{{ $colors[$project->status] ?? 'secondary' }}">
        <i class="fas fa-circle-notch fa-xs me-1 opacity-75"></i>
        {{ $statusLabels[$project->status] ?? $project->status }}
      </span>
    </div>
    <div class="col-6 text-end">
      <small class="text-secondary d-block mb-1">Úkoly</small>
      <span class="badge bg-light text-dark">
        <i class="fas fa-tasks fa-xs me-1 opacity-75"></i>
        {{ $project->tasks_count ?? 0 }}
      </span>
    </div>
  </div>

  <hr class="my-3">

  <div class="mb-3">
    <small class="text-secondary d-block mb-1">
      <i class="fas fa-building fa-xs me-1 opacity-75"></i>Klient
    </small>
    <a href="{{ route('clients.show', $project->client) }}" class="text-sm fw-500">
      {{ $project->client->name }} <i class="fas fa-external-link-alt fa-xs ms-1 opacity-75"></i>
    </a>
  </div>

  @if($project->due_date)
  <div class="mb-3">
    <small class="text-secondary d-block mb-1">
      <i class="fas fa-calendar fa-xs me-1 opacity-75"></i>Termín
    </small>
    <span class="text-sm">{{ $project->due_date->format('d.m.Y') }}</span>
  </div>
  @endif

  @if($project->creator)
  <div class="mb-3">
    <small class="text-secondary d-block mb-1">
      <i class="fas fa-user fa-xs me-1 opacity-75"></i>Vytvořil
    </small>
    <span class="text-sm">{{ $project->creator->name }}</span>
  </div>
  @endif

  @if($project->users->count())
  <div class="mb-3">
    <small class="text-secondary d-block mb-2">
      <i class="fas fa-users fa-xs me-1 opacity-75"></i>TÝM ({{ $project->users->count() }})
    </small>
    <div class="d-flex flex-wrap gap-1">
      @foreach($project->users as $member)
        <span class="badge bg-light text-dark" title="{{ $member->pivot->role }}">
          {{ $member->name }}
        </span>
      @endforeach
    </div>
  </div>
  @endif

  <hr class="my-3">

  <div class="btn-group btn-group-sm w-100" role="group">
    @can('update', $project)
      <a href="{{ route('projects.edit', $project) }}" class="btn btn-outline-secondary" title="Upravit">
        <i class="fas fa-edit me-1"></i>Upravit
      </a>
    @endcan
    <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-primary" title="Otevřít v novém okně">
      <i class="fas fa-external-link-alt me-1"></i>Otevřít
    </a>
  </div>
</div>

<div class="modal-footer border-top">
  <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Zavřít</button>
  @can('update', $project)
    <a href="{{ route('projects.edit', $project) }}" class="btn bg-gradient-primary btn-sm">
      <i class="fas fa-edit me-1"></i> Upravit
    </a>
  @endcan
  <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-primary btn-sm">
    <i class="fas fa-expand me-1"></i> Otevřít na plné stránce
  </a>
</div>
