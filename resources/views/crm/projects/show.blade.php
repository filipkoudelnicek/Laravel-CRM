@extends('layouts.user_type.auth')

@section('content')
@php $colors = ['planned'=>'secondary','active'=>'success','on_hold'=>'warning','done'=>'info'] @endphp
<div class="row">
  {{-- Project Info --}}
  <div class="col-lg-4">
    <div class="card mb-4">
      <div class="card-header pb-0 d-flex justify-content-between align-items-center">
        <h5 class="mb-0">{{ $project->name }}</h5>
        @can('update', $project)
          <a href="{{ route('projects.edit', $project) }}" class="btn btn-outline-secondary btn-sm">Upravit</a>
        @endcan
      </div>
      <div class="card-body pt-2">
        <p class="text-sm text-secondary mb-2">{{ $project->description }}</p>
        <ul class="list-group list-group-flush">
          <li class="list-group-item ps-0 border-0">
            <small class="text-secondary">Klient</small><br>
            <a href="{{ route('clients.show', $project->client) }}" class="font-weight-bold text-sm">{{ $project->client->name }}</a>
          </li>
          <li class="list-group-item ps-0 border-0">
            <small class="text-secondary">Stav</small><br>
            <span class="badge bg-gradient-{{ $colors[$project->status] ?? 'secondary' }}">
              {{ ['planned'=>'Plánovaný','active'=>'Aktivní','on_hold'=>'Pozastavený','done'=>'Dokončený'][$project->status] ?? $project->status }}
            </span>
          </li>
          @if($project->due_date)
          <li class="list-group-item ps-0 border-0">
            <small class="text-secondary">Termín</small><br>
            <span class="text-sm">{{ $project->due_date->format('d.m.Y') }}</span>
          </li>
          @endif
          <li class="list-group-item ps-0 border-0">
            <small class="text-secondary">Vytvořil</small><br>
            <span class="text-sm">{{ $project->creator->name ?? '—' }}</span>
          </li>
        </ul>
      </div>
    </div>

    {{-- Members --}}
    <div class="card mb-4">
      <div class="card-header pb-0">
        <h6 class="mb-0">Členové ({{ $project->users->count() }})</h6>
      </div>
      <div class="card-body pt-2">
        @foreach($project->users as $member)
          <div class="d-flex justify-content-between align-items-center mb-2">
            <div>
              <p class="text-sm font-weight-bold mb-0">{{ $member->name }}</p>
              <p class="text-xs text-secondary mb-0">{{ $member->pivot->role }}</p>
            </div>
            @can('manageMembers', $project)
              <form method="POST" action="{{ route('projects.detach-user', [$project, $member]) }}" class="d-inline"
                    onsubmit="return confirm('Odebrat člena?')">
                @csrf @method('DELETE')
                <button class="btn btn-link text-danger text-xs p-0">Odebrat</button>
              </form>
            @endcan
          </div>
        @endforeach

        @can('manageMembers', $project)
          <hr class="my-2">
          <form method="POST" action="{{ route('projects.attach-user', $project) }}">
            @csrf
            <div class="d-flex gap-2">
              <select name="user_id" class="form-select form-select-sm" required>
                <option value="">Přidat člena…</option>
                @foreach($allUsers as $u)
                  <option value="{{ $u->id }}">{{ $u->name }}</option>
                @endforeach
              </select>
              <button class="btn bg-gradient-primary btn-sm px-2 mb-0">Přidat</button>
            </div>
          </form>
        @endcan
      </div>
    </div>
  </div>

  {{-- Finance --}}
  <div class="col-lg-8">
    <div class="row mb-4">
      @if($project->estimated_cost)
      <div class="col-md-4">
        <div class="card"><div class="card-body p-3 text-center">
          <p class="text-sm mb-0 text-secondary">Odhadované náklady</p>
          <h6 class="mb-0">{{ number_format($project->estimated_cost, 0, ',', ' ') }} Kč</h6>
        </div></div>
      </div>
      @endif
      @if($project->hourly_rate)
      <div class="col-md-4">
        <div class="card"><div class="card-body p-3 text-center">
          <p class="text-sm mb-0 text-secondary">Hodinová sazba</p>
          <h6 class="mb-0">{{ number_format($project->hourly_rate, 0, ',', ' ') }} Kč/h</h6>
        </div></div>
      </div>
      @endif
      <div class="col-md-4">
        <div class="card"><div class="card-body p-3 text-center">
          <p class="text-sm mb-0 text-secondary">Fakturováno</p>
          <h6 class="mb-0">{{ number_format($project->invoices()->sum('total'), 0, ',', ' ') }} Kč</h6>
        </div></div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-lg-4"></div>
  {{-- Tasks --}}
  <div class="col-lg-8">
    <div class="card mb-4">
      <div class="card-header pb-0 d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Úkoly ({{ $project->tasks->count() }})</h6>
        <a href="{{ route('tasks.create') }}?project_id={{ $project->id }}" class="btn bg-gradient-primary btn-sm">+ Nový úkol</a>
      </div>
      <div class="card-body px-0 pt-0 pb-2">
        <div class="table-responsive p-0">
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Název</th>
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
              @forelse ($project->tasks as $task)
              <tr>
                <td>
                  <a href="{{ route('tasks.show', $task) }}" class="text-sm font-weight-bold text-dark ms-2">
                    {{ $task->title }}
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
              @empty
              <tr><td colspan="4" class="text-center py-3 text-sm text-secondary">Žádné úkoly.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

