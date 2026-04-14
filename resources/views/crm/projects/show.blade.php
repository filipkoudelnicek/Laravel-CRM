@extends('layouts.user_type.auth')

@section('content')
@php $colors = ['planned'=>'secondary','active'=>'success','on_hold'=>'warning','done'=>'info'] @endphp

{{-- Header --}}
<div class="row mb-4">
  <div class="col-12">
    <div class="d-flex justify-content-between align-items-start">
      <div>
        <h3 class="mb-1">
          <i class="fas fa-folder me-2 text-primary opacity-75"></i>{{ $project->name }}
        </h3>
        @if($project->description)
          <p class="text-secondary mb-0">{{ $project->description }}</p>
        @endif
      </div>
      <div class="btn-group btn-group-sm" role="group">
        @can('update', $project)
          <a href="{{ route('projects.edit', $project) }}" class="btn btn-outline-secondary">
            <i class="fas fa-edit me-1"></i> Upravit
          </a>
        @endcan
        @can('delete', $project)
          <form method="POST" action="{{ route('projects.destroy', $project) }}" class="d-inline" onsubmit="return confirm('Smazat projekt?')">
            @csrf @method('DELETE')
            <button class="btn btn-outline-danger">
              <i class="fas fa-trash me-1"></i> Smazat
            </button>
          </form>
        @endcan
      </div>
    </div>
  </div>
</div>

<div class="row">
  {{-- Project Info --}}
  <div class="col-lg-4">
    <div class="card mb-4">
      <div class="card-header pb-3 pt-4">
        <h6 class="mb-0">
          <i class="fas fa-info-circle me-2 text-primary opacity-75"></i>Informace
        </h6>
      </div>
      <div class="card-body">
        {{-- Status & Client --}}
        <div class="row g-3 mb-4">
          <div class="col-12">
            <small class="text-secondary d-block mb-2">
              <i class="fas fa-circle-notch fa-xs me-1 opacity-75"></i>STAV
            </small>
            <span class="badge bg-gradient-{{ $colors[$project->status] ?? 'secondary' }} px-3 py-2">
              {{ ['planned'=>'Plánovaný','active'=>'Aktivní','on_hold'=>'Pozastavený','done'=>'Dokončený'][$project->status] ?? $project->status }}
            </span>
          </div>
        </div>

        <hr class="my-3">

        {{-- Client --}}
        <div class="mb-4">
          <small class="text-secondary d-block mb-2">
            <i class="fas fa-building fa-xs me-1 opacity-75"></i>KLIENT
          </small>
          <a href="{{ route('clients.show', $project->client) }}" class="text-sm fw-500">
            {{ $project->client->name }} <i class="fas fa-external-link-alt fa-xs ms-1 opacity-75"></i>
          </a>
        </div>

        @if($project->due_date)
        <div class="mb-4">
          <small class="text-secondary d-block mb-2">
            <i class="fas fa-calendar fa-xs me-1 opacity-75"></i>TERMÍN
          </small>
          <span class="text-sm {{ $project->due_date->isPast() && $project->status !== 'done' ? 'text-danger fw-bold' : '' }}">
            {{ $project->due_date->format('d.m.Y') }}
          </span>
        </div>
        @endif

        <div class="mb-4">
          <small class="text-secondary d-block mb-2">
            <i class="fas fa-user fa-xs me-1 opacity-75"></i>VYTVOŘIL
          </small>
          <span class="text-sm">{{ $project->creator->name ?? '—' }}</span>
        </div>

        @if($project->web_url)
        <div class="mb-4">
          <small class="text-secondary d-block mb-2">
            <i class="fas fa-globe fa-xs me-1 opacity-75"></i>WEB
          </small>
          <a href="{{ $project->web_url }}" target="_blank" rel="noopener noreferrer" class="text-sm fw-500">
            {{ $project->web_url }} <i class="fas fa-external-link-alt fa-xs ms-1 opacity-75"></i>
          </a>
        </div>
        @endif

        {{-- Finance --}}
        <hr class="my-3">

        @if($project->estimated_cost)
        <div class="mb-3">
          <small class="text-secondary d-block mb-1">
            <i class="fas fa-tag fa-xs me-1 opacity-75"></i>ODHAD NÁKLADŮ
          </small>
          <span class="text-sm fw-bold">{{ number_format($project->estimated_cost, 0, ',', ' ') }} Kč</span>
        </div>
        @endif

        @if($project->hourly_rate)
        <div class="mb-3">
          <small class="text-secondary d-block mb-1">
            <i class="fas fa-hourglass-end fa-xs me-1 opacity-75"></i>SAZBA/HODINA
          </small>
          <span class="text-sm fw-bold">{{ number_format($project->hourly_rate, 0, ',', ' ') }} Kč</span>
        </div>
        @endif

        <div class="mb-0">
          <small class="text-secondary d-block mb-1">
            <i class="fas fa-receipt fa-xs me-1 opacity-75"></i>FAKTUROVÁNO
          </small>
          <span class="text-sm fw-bold">{{ number_format($finance['invoicedTotal'], 0, ',', ' ') }} Kč</span>
        </div>
      </div>
    </div>

    {{-- Members --}}
    <div class="card mb-4">
      <div class="card-header pb-3 pt-4">
        <h6 class="mb-0">
          <i class="fas fa-users me-2 text-primary opacity-75"></i>Členové ({{ $project->users->count() }})
        </h6>
      </div>
      <div class="card-body">
        @forelse($project->users as $member)
          <div class="d-flex justify-content-between align-items-center mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
            <div>
              <p class="text-sm fw-bold mb-1">{{ $member->name }}</p>
              <small class="text-secondary">{{ $member->pivot->role }}</small>
            </div>
            @can('manageMembers', $project)
              <form method="POST" action="{{ route('projects.detach-user', [$project, $member]) }}" class="d-inline"
                    onsubmit="return confirm('Odebrat člena?')">
                @csrf @method('DELETE')
                <button class="btn btn-link text-danger text-xs p-0" title="Odebrat">
                  <i class="fas fa-times"></i>
                </button>
              </form>
            @endcan
          </div>
        @empty
          <p class="text-sm text-secondary mb-0">Žádní členové.</p>
        @endforelse

        @can('manageMembers', $project)
          <hr class="my-3">
          <form method="POST" action="{{ route('projects.attach-user', $project) }}">
            @csrf
            <div class="d-flex gap-2">
              <select name="user_id" class="form-select form-select-sm" required>
                <option value="">Přidat člena…</option>
                @foreach($allUsers as $u)
                  @unless($project->users->contains($u))
                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                  @endunless
                @endforeach
              </select>
              <button class="btn bg-gradient-primary btn-sm px-3 mb-0">
                <i class="fas fa-plus me-1"></i>Přidat
              </button>
            </div>
          </form>
        @endcan
      </div>
    </div>
  </div>

  {{-- Tasks & Finance --}}
  <div class="col-lg-8">
    {{-- Finance Stats --}}
    <div class="row mb-4">
      @if($project->estimated_cost)
      <div class="col-md-6">
        <div class="card h-100">
          <div class="card-body p-3">
            <small class="text-secondary d-block mb-2">
              <i class="fas fa-tag fa-xs me-1 opacity-75"></i>ODHAD NÁKLADŮ
            </small>
            <h6 class="mb-0 fw-bold">{{ number_format($project->estimated_cost, 0, ',', ' ') }} Kč</h6>
          </div>
        </div>
      </div>
      @endif
      @if($project->hourly_rate)
      <div class="col-md-6">
        <div class="card h-100">
          <div class="card-body p-3">
            <small class="text-secondary d-block mb-2">
              <i class="fas fa-hourglass-end fa-xs me-1 opacity-75"></i>SAZBA/HODINA
            </small>
            <h6 class="mb-0 fw-bold">{{ number_format($project->hourly_rate, 0, ',', ' ') }} Kč</h6>
          </div>
        </div>
      </div>
      @endif
      <div class="col-md-{{ $project->hourly_rate || $project->estimated_cost ? '6' : '12' }}">
        <div class="card h-100">
          <div class="card-body p-3">
            <small class="text-secondary d-block mb-2">
              <i class="fas fa-receipt fa-xs me-1 opacity-75"></i>FAKTUROVÁNO
            </small>
            <h6 class="mb-0 fw-bold">{{ number_format($finance['invoicedTotal'], 0, ',', ' ') }} Kč</h6>
          </div>
        </div>
      </div>
    </div>

    {{-- Tasks --}}
    <div class="card mb-4">
      <div class="card-header pb-0 d-flex justify-content-between align-items-center">
        <h6 class="mb-0">
          <i class="fas fa-tasks me-2 opacity-75"></i>Úkoly (<span class="badge bg-light text-dark">{{ $project->tasks->count() }}</span>)
        </h6>
        <a href="{{ route('tasks.create') }}?project_id={{ $project->id }}" class="btn btn-outline-primary btn-sm">
          <i class="fas fa-plus me-1"></i>Nový úkol
        </a>
      </div>
      <div class="card-body px-0 pt-0 pb-2">
        @if($project->tasks->count())
        <div class="table-responsive p-0">
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Název</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Stav</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Priorita</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Termín</th>
              </tr>
            </thead>
            <tbody>
              @php
                $taskColors = ['todo'=>'secondary','in_progress'=>'primary','review'=>'warning','done'=>'success'];
                $prioColors = ['low'=>'info','medium'=>'warning','high'=>'danger'];
                $taskStatusLabels = ['todo'=>'K řešení','in_progress'=>'Probíhá','review'=>'Ke kontrole','done'=>'Dokončeno'];
                $taskPrioLabels = ['low'=>'Nízká','medium'=>'Střední','high'=>'Vysoká'];
              @endphp
              @foreach($project->tasks as $task)
              <tr class="align-middle">
                <td class="ps-3">
                  <a href="{{ route('tasks.show', $task) }}" class="text-sm fw-bold text-dark">
                    {{ Str::limit($task->title, 40) }}
                  </a>
                </td>
                <td class="text-center">
                  <span class="badge badge-sm bg-gradient-{{ $taskColors[$task->status] ?? 'secondary' }}">
                    {{ $taskStatusLabels[$task->status] ?? $task->status }}
                  </span>
                </td>
                <td class="text-center">
                  <span class="badge badge-sm bg-gradient-{{ $prioColors[$task->priority] ?? 'secondary' }}">
                    {{ $taskPrioLabels[$task->priority] ?? $task->priority }}
                  </span>
                </td>
                <td class="text-center"><span class="text-xs">{{ $task->due_date?->format('d.m.Y') ?? '—' }}</span></td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        @else
        <div class="text-center py-4">
          <p class="text-sm text-secondary">Žádné úkoly. <a href="{{ route('tasks.create') }}?project_id={{ $project->id }}">Vytvořit první</a></p>
        </div>
        @endif</div>
@endsection

