@props(['task'])

<div class="modal-header border-bottom">
  <h5 class="modal-title">{{ $task->title }}</h5>
  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
  @php
    $sc = ['todo'=>'secondary','in_progress'=>'primary','review'=>'warning','done'=>'success'];
    $pc = ['low'=>'info','medium'=>'warning','high'=>'danger'];
  @endphp

  @if($task->description)
    <div class="mb-4">
      <h6 class="text-uppercase text-xs text-secondary font-weight-bold mb-2">Popis</h6>
      <p class="text-sm mb-0">{{ $task->description }}</p>
    </div>
  @endif

  <div class="row mb-3">
    <div class="col-6">
      <small class="text-secondary d-block">Stav</small>
      <span class="badge bg-gradient-{{ $sc[$task->status] }}">
        {{ ['todo'=>'K řešení','in_progress'=>'Probíhá','review'=>'Ke kontrole','done'=>'Dokončeno'][$task->status] ?? $task->status }}
      </span>
    </div>
    <div class="col-6 text-end">
      <small class="text-secondary d-block">Priorita</small>
      <span class="badge bg-gradient-{{ $pc[$task->priority] }}">
        {{ ['low'=>'Nízká','medium'=>'Střední','high'=>'Vysoká'][$task->priority] ?? $task->priority }}
      </span>
    </div>
  </div>

  <hr>

  <h6 class="text-uppercase text-xs text-secondary font-weight-bold mb-2"><i class="fas fa-folder me-1"></i>Projekt</h6>
  <a href="{{ route('projects.show', $task->project) }}" class="text-sm text-dark float-end" onclick="location.href=this.href; return false;">
    {{ $task->project->name }} <i class="fas fa-external-link-alt fa-xs ms-1"></i>
  </a>
  <div class="clearfix"></div>

  <hr>

  <h6 class="text-uppercase text-xs text-secondary font-weight-bold mb-2"><i class="fas fa-building me-1"></i>Klient</h6>
  <a href="{{ route('clients.show', $task->project->client) }}" class="text-sm text-dark float-end" onclick="location.href=this.href; return false;">
    {{ $task->project->client->name }} <i class="fas fa-external-link-alt fa-xs ms-1"></i>
  </a>
  <div class="clearfix"></div>

  @if($task->due_date)
    <hr>
    <small class="text-secondary">Termín</small>
    <p class="text-sm font-weight-bold {{ $task->due_date->isPast() && $task->status !== 'done' ? 'text-danger' : '' }}">
      {{ $task->due_date->format('d.m.Y') }}
    </p>
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
