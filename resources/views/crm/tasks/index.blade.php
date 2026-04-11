@extends('layouts.user_type.auth')

@section('content')
@php
  $sc = ['todo'=>'secondary','in_progress'=>'primary','review'=>'warning','done'=>'success'];
  $pc = ['low'=>'info','medium'=>'warning','high'=>'danger'];
  $statusLabels = ['todo'=>'K řešení','in_progress'=>'Probíhá','review'=>'Ke kontrole','done'=>'Dokončeno'];
  $priorityLabels = ['low'=>'Nízká','medium'=>'Střední','high'=>'Vysoká'];
@endphp

<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      <div class="card-header pb-3 pt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="mb-0">
            <i class="fas fa-tasks me-2 text-primary opacity-75"></i>Úkoly
          </h5>
          <a href="{{ route('tasks.create') }}" class="btn bg-gradient-primary btn-sm">
            <i class="fas fa-plus me-1"></i> Nový úkol
          </a>
        </div>
        
        {{-- Filters --}}
        <form method="GET" action="{{ route('tasks.index') }}" class="row g-2">
          <div class="col-auto">
            <input type="text" name="q" value="{{ request('q') }}" 
                   class="form-control form-control-sm" placeholder="Hledat úkol…">
          </div>
          <div class="col-auto">
            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
              <option value="">— Všechny stavy —</option>
              @foreach($statusLabels as $status => $label)
                <option value="{{ $status }}" @selected(request('status') === $status)>{{ $label }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-auto">
            <select name="priority" class="form-select form-select-sm" onchange="this.form.submit()">
              <option value="">— Všechny priority —</option>
              @foreach($priorityLabels as $priority => $label)
                <option value="{{ $priority }}" @selected(request('priority') === $priority)>{{ $label }}</option>
              @endforeach
            </select>
          </div>
          @if(request()->hasAny(['q','status','priority']))
            <div class="col-auto">
              <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-times me-1"></i> Vymazat
              </a>
            </div>
          @endif
        </form>
      </div>
      
      <div class="card-body px-0 py-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead class="table-light border-bottom">
              <tr>
                <th class="text-xs fw-bold text-secondary px-4 py-3">Úkol</th>
                <th class="text-xs fw-bold text-secondary px-4 py-3">Projekt</th>
                <th class="text-xs fw-bold text-secondary text-center px-4 py-3">Stav</th>
                <th class="text-xs fw-bold text-secondary text-center px-4 py-3">Priorita</th>
                <th class="text-xs fw-bold text-secondary text-center px-4 py-3">Termín</th>
                <th class="text-xs fw-bold text-secondary text-center px-4 py-3" style="width: 100px;">Akce</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($tasks as $task)
              <tr class="align-middle">
                <td class="px-4 py-3">
                  <button onclick="openDetailModal('{{ route('tasks.modal', $task) }}')" 
                          class="btn btn-link text-dark p-0 text-decoration-none text-start fw-500"
                          style="font-weight: 500;">
                    {{ $task->title }}
                  </button>
                  @if($task->description)
                    <br><small class="text-secondary">{{ Str::limit($task->description, 50) }}</small>
                  @endif
                </td>
                <td class="px-4 py-3">
                  <div class="text-sm">
                    <strong>{{ $task->project->name }}</strong>
                    <br><small class="text-secondary">{{ $task->project->client->name ?? '—' }}</small>
                  </div>
                </td>
                <td class="px-4 py-3 text-center">
                  <span class="badge bg-gradient-{{ $sc[$task->status] ?? 'secondary' }} px-3">
                    <i class="fas fa-circle-notch fa-xs me-1 opacity-75"></i>
                    {{ $statusLabels[$task->status] ?? $task->status }}
                  </span>
                </td>
                <td class="px-4 py-3 text-center">
                  <span class="badge bg-gradient-{{ $pc[$task->priority] ?? 'secondary' }} px-3">
                    <i class="fas fa-flag fa-xs me-1 opacity-75"></i>
                    {{ $priorityLabels[$task->priority] ?? $task->priority }}
                  </span>
                </td>
                <td class="px-4 py-3 text-center">
                  <small class="text-secondary">
                    @if($task->due_date)
                      <i class="fas fa-calendar fa-xs me-1 opacity-75"></i>
                      {{ $task->due_date->format('d.m.Y') }}
                    @else
                      —
                    @endif
                  </small>
                </td>
                <td class="px-4 py-3 text-center">
                  <div class="btn-group btn-group-sm" role="group">
                    <a href="{{ route('tasks.show', $task) }}" class="btn btn-outline-secondary btn-sm" title="Otevřít">
                      <i class="fas fa-external-link-alt fa-sm"></i>
                    </a>
                    @can('update', $task)
                      <a href="{{ route('tasks.edit', $task) }}" class="btn btn-outline-secondary btn-sm" title="Upravit">
                        <i class="fas fa-edit fa-sm"></i>
                      </a>
                    @endcan
                    @can('delete', $task)
                      <form method="POST" action="{{ route('tasks.destroy', $task) }}" class="d-inline"
                            onsubmit="return confirm('Smazat tento úkol?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-outline-danger btn-sm" title="Smazat" type="submit">
                          <i class="fas fa-trash fa-sm"></i>
                        </button>
                      </form>
                    @endcan
                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="6" class="px-4 py-4">
                  <div class="text-center text-secondary">
                    <i class="fas fa-inbox fa-3x opacity-25 d-block mb-2"></i>
                    <small>Žádné úkoly nenalezeny.</small>
                  </div>
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        
        @if($tasks->hasPages())
          <div class="card-footer bg-white px-4 py-3 border-top">
            {{ $tasks->links() }}
          </div>
        @endif
      </div>
    </div>
  </div>
</div>

@include('components.detail-modal')
@endsection

