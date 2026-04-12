@props(['task'])

<div class="modal-header border-bottom bg-transparent">
  <h5 class="modal-title">{{ $task->title }}</h5>
</div>

<div class="modal-body">
  @php
    $sc = ['todo'=>'secondary','in_progress'=>'primary','review'=>'warning','done'=>'success'];
    $pc = ['low'=>'info','medium'=>'warning','high'=>'danger'];
  @endphp

  @if($task->description)
    <div class="mb-3">
      <h6 class="text-uppercase text-xs text-secondary font-weight-bold mb-2">Popis</h6>
      <p class="text-sm mb-0">{{ $task->description }}</p>
    </div>
  @endif

  <div class="row mb-3">
    <div class="col-6">
      <small class="text-secondary d-block">Stav</small>
      <span class="badge bg-gradient-{{ $sc[$task->status] }}">
        <i class="fas fa-circle-notch fa-xs me-1 opacity-75"></i>
        {{ ['todo'=>'K řešení','in_progress'=>'Probíhá','review'=>'Ke kontrole','done'=>'Dokončeno'][$task->status] ?? $task->status }}
      </span>
    </div>
    <div class="col-6 text-end">
      <small class="text-secondary d-block">Priorita</small>
      <span class="badge bg-gradient-{{ $pc[$task->priority] }}">
        <i class="fas fa-flag fa-xs me-1 opacity-75"></i>
        {{ ['low'=>'Nízká','medium'=>'Střední','high'=>'Vysoká'][$task->priority] ?? $task->priority }}
      </span>
    </div>
  </div>

  <hr class="my-3">

  <div class="mb-3">
    <small class="text-secondary d-block mb-1">
      <i class="fas fa-folder fa-xs me-1 opacity-75"></i>Projekt
    </small>
    <a href="{{ route('projects.show', $task->project) }}" class="text-sm fw-500">
      {{ $task->project->name }} <i class="fas fa-external-link-alt fa-xs ms-1 opacity-75"></i>
    </a>
  </div>

  <div class="mb-3">
    <small class="text-secondary d-block mb-1">
      <i class="fas fa-building fa-xs me-1 opacity-75"></i>Klient
    </small>
    <a href="{{ route('clients.show', $task->project->client) }}" class="text-sm fw-500">
      {{ $task->project->client->name }} <i class="fas fa-external-link-alt fa-xs ms-1 opacity-75"></i>
    </a>
  </div>

  @if($task->due_date)
  <div class="mb-3">
    <small class="text-secondary d-block mb-1">
      <i class="fas fa-calendar fa-xs me-1 opacity-75"></i>Termín
    </small>
    <span class="text-sm {{ $task->due_date->isPast() && $task->status !== 'done' ? 'text-danger fw-bold' : '' }}">
      {{ $task->due_date->format('d.m.Y') }}
    </span>
  </div>
  @endif

  @if($task->assignees->count())
  <div class="mb-3">
    <small class="text-secondary d-block mb-2">
      <i class="fas fa-users fa-xs me-1 opacity-75"></i>PŘIŘAZENÍ
    </small>
    <div class="d-flex flex-wrap gap-1">
      @foreach($task->assignees as $assignee)
        <span class="badge bg-light text-dark">{{ $assignee->name }}</span>
      @endforeach
    </div>
  </div>
  @endif

  <hr class="my-3">

  <div class="btn-group btn-group-sm w-100" role="group">
    @can('update', $task)
      <a href="{{ route('tasks.edit', $task) }}" class="btn btn-outline-secondary" title="Upravit">
        <i class="fas fa-edit me-1"></i>Upravit
      </a>
    @endcan
    <a href="{{ route('tasks.show', $task) }}" class="btn btn-outline-primary" title="Otevřít v novém okně">
      <i class="fas fa-external-link-alt me-1"></i>Otevřít
    </a>
  </div>
</div>
  @endif

  @if($task->assignees->count())
    <hr>
    <small class="text-secondary d-block mb-2">Přiřazení</small>
    <div class="d-flex flex-wrap gap-1">
      @foreach($task->assignees as $assignee)
        <span class="badge bg-gradient-light text-dark">{{ $assignee->name }}</span>
      @endforeach
    </div>
  @endif
</div>

<div class="modal-footer border-top">
  <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Zavřít</button>
  @can('update', $task)
    <a href="{{ route('tasks.edit', $task) }}" class="btn bg-gradient-primary btn-sm">
      <i class="fas fa-edit me-1"></i> Upravit
    </a>
  @endcan
  <a href="{{ route('tasks.show', $task) }}" class="btn btn-outline-primary btn-sm">
    <i class="fas fa-expand me-1"></i> Otevřít na plné stránce
  </a>
</div>
