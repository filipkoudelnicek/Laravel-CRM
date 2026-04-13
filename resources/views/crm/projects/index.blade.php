@extends('layouts.user_type.auth')

@section('content')
@php
  $colors = ['planned'=>'secondary','active'=>'success','on_hold'=>'warning','done'=>'info'];
  $statusLabels = ['planned'=>'Plánovaný','active'=>'Aktivní','on_hold'=>'Pozastavený','done'=>'Dokončený'];
@endphp

<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      <div class="card-header pb-3 pt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="mb-0">
            <i class="fas fa-folder me-2 text-primary opacity-75"></i>Projekty
          </h5>
          @can('create', \App\Models\Project::class)
            <a href="{{ route('projects.create') }}" class="btn bg-gradient-primary btn-sm">
              <i class="fas fa-plus me-1"></i> Nový projekt
            </a>
          @endcan
        </div>
        
        {{-- Filters --}}
        <form method="GET" action="{{ route('projects.index') }}" class="row g-2">
          <div class="col-auto">
            <input type="text" name="q" value="{{ request('q') }}" 
                   class="form-control form-control-sm" placeholder="Hledat projekt…">
          </div>
          <div class="col-auto">
            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
              <option value="">— Všechny stavy —</option>
              @foreach($statusLabels as $status => $label)
                <option value="{{ $status }}" @selected(request('status') === $status)>{{ $label }}</option>
              @endforeach
            </select>
          </div>
          @if(request('q') || request('status'))
            <div class="col-auto">
              <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary btn-sm">
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
                <th class="text-xs fw-bold text-secondary px-4 py-3">Projekt</th>
                <th class="text-xs fw-bold text-secondary px-4 py-3">Klient</th>
                <th class="text-xs fw-bold text-secondary text-center px-4 py-3">Stav</th>
                <th class="text-xs fw-bold text-secondary text-center px-4 py-3">Úkoly</th>
                <th class="text-xs fw-bold text-secondary text-center px-4 py-3">Termín</th>
                <th class="text-xs fw-bold text-secondary text-center px-4 py-3" style="width: 100px;">Akce</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($projects as $project)
              <tr class="align-middle">
                <td class="px-4 py-3">
                  <button onclick="openDetailModal('{{ route('projects.modal', $project) }}')" 
                          class="btn btn-link text-dark p-0 text-decoration-none text-start fw-500"
                          style="font-weight: 500;">
                    {{ $project->name }}
                  </button>
                  @if($project->description)
                    <br><small class="text-secondary">{{ Str::limit($project->description, 50) }}</small>
                  @endif
                </td>
                <td class="px-4 py-3">
                  <button onclick="openDetailModal('{{ route('clients.modal', $project->client) }}')" 
                          class="btn btn-link text-secondary p-0 text-decoration-none fw-500">
                    {{ $project->client->name }}
                  </button>
                </td>
                <td class="px-4 py-3 text-center">
                  <span class="badge bg-gradient-{{ $colors[$project->status] ?? 'secondary' }} px-3">
                    <i class="fas fa-circle-notch fa-xs me-1 opacity-75"></i>
                    {{ $statusLabels[$project->status] ?? $project->status }}
                  </span>
                </td>
                <td class="px-4 py-3 text-center">
                  <span class="badge bg-light text-dark px-2">
                    <i class="fas fa-tasks fa-xs me-1 opacity-75"></i>
                    {{ $project->tasks_count }}
                  </span>
                </td>
                <td class="px-4 py-3 text-center">
                  <small class="text-secondary">
                    @if($project->due_date)
                      <i class="fas fa-calendar fa-xs me-1 opacity-75"></i>
                      {{ $project->due_date->format('d.m.Y') }}
                    @else
                      —
                    @endif
                  </small>
                </td>
                <td class="px-4 py-3 text-center">
                  <div class="btn-group btn-group-sm" role="group">
                    <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-secondary btn-sm" title="Otevřít">
                      <i class="fas fa-external-link-alt fa-sm"></i>
                    </a>
                    @can('update', $project)
                      <a href="{{ route('projects.edit', $project) }}" class="btn btn-outline-secondary btn-sm" title="Upravit">
                        <i class="fas fa-edit fa-sm"></i>
                      </a>
                    @endcan
                    @can('delete', $project)
                      <form method="POST" action="{{ route('projects.destroy', $project) }}" class="d-inline"
                            onsubmit="return confirm('Smazat projekt?')">
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
                    <small>Žádné projekty nenalezeny.</small>
                  </div>
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        
        @if($projects->hasPages())
          <div class="card-footer bg-white px-4 py-3 border-top">
            {{ $projects->links() }}
          </div>
        @endif
      </div>
    </div>
  </div>
</div>

@include('components.detail-modal')
@endsection

