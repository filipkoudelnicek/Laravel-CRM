@extends('layouts.user_type.auth')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card mb-4 mx-4">
      <div class="card-header pb-0">
        <div class="d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Úkoly</h5>
          <a href="{{ route('tasks.create') }}" class="btn bg-gradient-primary btn-sm">+ Nový úkol</a>
        </div>
        <form method="GET" action="{{ route('tasks.index') }}" class="mt-3 mb-0">
          <div class="d-flex gap-2 flex-wrap">
            <div class="input-group input-group-sm" style="max-width:280px">
              <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Hledat úkoly…">
              <button class="btn btn-outline-secondary mb-0" type="submit">Hledat</button>
            </div>
            <select name="status" class="form-select form-select-sm" style="max-width:150px" onchange="this.form.submit()">
              <option value="">Všechny stavy</option>
              @foreach(['todo'=>'K řešení','in_progress'=>'Probíhá','review'=>'Ke kontrole','done'=>'Dokončeno'] as $s => $label)
                <option value="{{ $s }}" @selected(request('status') === $s)>{{ $label }}</option>
              @endforeach
            </select>
            <select name="priority" class="form-select form-select-sm" style="max-width:130px" onchange="this.form.submit()">
              <option value="">Všechny priority</option>
              @foreach(['low'=>'Nízká','medium'=>'Střední','high'=>'Vysoká'] as $p => $label)
                <option value="{{ $p }}" @selected(request('priority') === $p)>{{ $label }}</option>
              @endforeach
            </select>
            @if(request()->hasAny(['q','status','priority']))
              <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary btn-sm">Vymazat</a>
            @endif
          </div>
        </form>
      </div>
      <div class="card-body px-0 pt-0 pb-2">
        <div class="table-responsive p-0">
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Úkol</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Projekt / Klient</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Stav</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Priorita</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Termín</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Akce</th>
              </tr>
            </thead>
            <tbody>
              @php
                $sc = ['todo'=>'secondary','in_progress'=>'primary','review'=>'warning','done'=>'success'];
                $pc = ['low'=>'info','medium'=>'warning','high'=>'danger'];
                $statusLabels = ['todo'=>'K řešení','in_progress'=>'Probíhá','review'=>'Ke kontrole','done'=>'Dokončeno'];
                $priorityLabels = ['low'=>'Nízká','medium'=>'Střední','high'=>'Vysoká'];
              @endphp
              @forelse ($tasks as $task)
              <tr>
                <td>
                  <div class="d-flex px-2 py-1">
                    <div class="d-flex flex-column justify-content-center">
                      <h6 class="mb-0 text-sm">
                        <button onclick="openDetailModal('{{ route('tasks.modal', $task) }}')" 
                                class="btn btn-link text-dark p-0 text-decoration-none text-start"
                                style="font-weight: 500;">
                          {{ $task->title }}
                        </button>
                      </h6>
                    </div>
                  </div>
                </td>
                <td>
                  <p class="text-xs font-weight-bold mb-0">{{ $task->project->name }}</p>
                  <p class="text-xs text-secondary mb-0">{{ $task->project->client->name ?? '' }}</p>
                </td>
                <td class="text-center">
                  <span class="badge badge-sm bg-gradient-{{ $sc[$task->status] ?? 'secondary' }}">
                    {{ $statusLabels[$task->status] ?? $task->status }}
                  </span>
                </td>
                <td class="text-center">
                  <span class="badge badge-sm bg-gradient-{{ $pc[$task->priority] ?? 'secondary' }}">
                    {{ $priorityLabels[$task->priority] ?? $task->priority }}
                  </span>
                </td>
                <td class="text-center"><span class="text-xs">{{ $task->due_date?->format('d.m.Y') ?? '—' }}</span></td>
                <td class="text-center">
                  <div class="btn-group btn-group-sm" role="group">
                    <a href="{{ route('tasks.show', $task) }}" class="btn btn-outline-secondary" title="Otevřít">
                      <i class="fas fa-external-link-alt fa-xs"></i>
                    </a>
                    @can('update', $task)
                      <a href="{{ route('tasks.edit', $task) }}" class="btn btn-outline-secondary" title="Upravit">
                        <i class="fas fa-edit fa-xs"></i>
                      </a>
                    @endcan
                    @can('delete', $task)
                      <form method="POST" action="{{ route('tasks.destroy', $task) }}" class="d-inline"
                            onsubmit="return confirm('Smazat tento úkol?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-outline-danger" title="Smazat" type="submit">
                          <i class="fas fa-trash fa-xs"></i>
                        </button>
                      </form>
                    @endcan
                  </div>
                </td>
              </tr>
              @empty
              <tr><td colspan="6" class="text-center py-3 text-sm text-secondary">Žádné úkoly nenalezeny.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <div class="px-4 pt-3">{{ $tasks->links() }}</div>
      </div>
    </div>
  </div>
</div>

@include('components.detail-modal')
@endsection

