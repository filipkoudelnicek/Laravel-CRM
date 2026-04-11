@props(['project'])

<div class="modal-header border-bottom">
  <h5 class="modal-title">{{ $project->name }}</h5>
  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
  @php
    $colors = ['planned'=>'secondary','active'=>'success','on_hold'=>'warning','done'=>'info'];
    $statusLabels = ['planned'=>'Plánovaný','active'=>'Aktivní','on_hold'=>'Pozastavený','done'=>'Dokončený'];
  @endphp

  @if($project->description)
    <div class="mb-4">
      <h6 class="text-uppercase text-xs text-secondary font-weight-bold mb-2">Popis</h6>
      <p class="text-sm mb-0">{{ $project->description }}</p>
    </div>
  @endif

  <div class="row mb-3">
    <div class="col-6">
      <small class="text-secondary d-block">Stav</small>
      <span class="badge bg-gradient-{{ $colors[$project->status] ?? 'secondary' }}">
        {{ $statusLabels[$project->status] ?? $project->status }}
      </span>
    </div>
    <div class="col-6 text-end">
      <small class="text-secondary d-block">Počet úkolů</small>
      <h6 class="mb-0">{{ $project->tasks_count ?? 0 }}</h6>
    </div>
  </div>

  <hr>

  <h6 class="text-uppercase text-xs text-secondary font-weight-bold mb-2"><i class="fas fa-building me-1"></i>Klient</h6>
  <a href="{{ route('clients.show', $project->client) }}" class="text-sm text-dark float-end" onclick="location.href=this.href; return false;">
    {{ $project->client->name }} <i class="fas fa-external-link-alt fa-xs ms-1"></i>
  </a>
  <div class="clearfix"></div>

  @if($project->due_date)
    <hr>
    <small class="text-secondary">Termín</small>
    <p class="text-sm font-weight-bold">{{ $project->due_date->format('d.m.Y') }}</p>
  @endif

  @if($project->users->count())
    <hr>
    <small class="text-secondary d-block mb-2">Tým</small>
    <div class="d-flex flex-wrap gap-1">
      @foreach($project->users as $member)
        <span class="badge bg-gradient-light text-dark">{{ $member->name }}</span>
      @endforeach
    </div>
  @endif
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
