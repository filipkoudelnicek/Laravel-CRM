@extends('layouts.user_type.auth')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card mb-4 mx-4">
      <div class="card-header pb-0">
        <div class="d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Projekty</h5>
          @can('create', \App\Models\Project::class)
            <a href="{{ route('projects.create') }}" class="btn bg-gradient-primary btn-sm">+ Nový projekt</a>
          @endcan
        </div>
        <form method="GET" action="{{ route('projects.index') }}" class="mt-3 mb-0">
          <div class="d-flex gap-2 flex-wrap">
            <div class="input-group input-group-sm" style="max-width:300px">
              <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Hledat projekty…">
              <button class="btn btn-outline-secondary mb-0" type="submit">Hledat</button>
            </div>
            <select name="status" class="form-select form-select-sm" style="max-width:150px" onchange="this.form.submit()">
              <option value="">Všechny stavy</option>
              @foreach(['planned'=>'Plánovaný','active'=>'Aktivní','on_hold'=>'Pozastavený','done'=>'Dokončený'] as $s => $label)
                <option value="{{ $s }}" @selected(request('status') === $s)>{{ $label }}</option>
              @endforeach
            </select>
            @if(request('q') || request('status'))
              <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary btn-sm">Vymazat</a>
            @endif
          </div>
        </form>
      </div>
      <div class="card-body px-0 pt-0 pb-2">
        <div class="table-responsive p-0">
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Projekt</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Klient</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Stav</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Úkoly</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Termín</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Akce</th>
              </tr>
            </thead>
            <tbody>
              @php
                $colors = ['planned'=>'secondary','active'=>'success','on_hold'=>'warning','done'=>'info'];
                $statusLabels = ['planned'=>'Plánovaný','active'=>'Aktivní','on_hold'=>'Pozastavený','done'=>'Dokončený'];
              @endphp
              @forelse ($projects as $project)
              <tr>
                <td>
                  <div class="d-flex px-2 py-1">
                    <div class="d-flex flex-column justify-content-center">
                      <h6 class="mb-0 text-sm">
                        <a href="{{ route('projects.show', $project) }}" class="text-dark">{{ $project->name }}</a>
                      </h6>
                    </div>
                  </div>
                </td>
                <td>
                  <p class="text-xs font-weight-bold mb-0">
                    <a href="{{ route('clients.show', $project->client) }}" class="text-secondary">
                      {{ $project->client->name }}
                    </a>
                  </p>
                </td>
                <td class="text-center">
                  <span class="badge badge-sm bg-gradient-{{ $colors[$project->status] ?? 'secondary' }}">
                    {{ $statusLabels[$project->status] ?? $project->status }}
                  </span>
                </td>
                <td class="text-center"><span class="text-xs">{{ $project->tasks_count }}</span></td>
                <td class="text-center"><span class="text-xs">{{ $project->due_date?->format('d.m.Y') ?? '—' }}</span></td>
                <td class="text-center">
                  <a href="{{ route('projects.show', $project) }}" class="text-secondary font-weight-bold text-xs me-2">Zobrazit</a>
                  @can('update', $project)
                    <a href="{{ route('projects.edit', $project) }}" class="text-secondary font-weight-bold text-xs me-2">Upravit</a>
                  @endcan
                  @can('delete', $project)
                    <form method="POST" action="{{ route('projects.destroy', $project) }}" class="d-inline"
                          onsubmit="return confirm('Smazat tento projekt a všechny jeho úkoly?')">
                      @csrf @method('DELETE')
                      <button class="btn btn-link text-danger font-weight-bold text-xs p-0 mb-0">Smazat</button>
                    </form>
                  @endcan
                </td>
              </tr>
              @empty
              <tr><td colspan="6" class="text-center py-3 text-sm text-secondary">Žádné projekty nenalezeny.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <div class="px-4 pt-3">{{ $projects->links() }}</div>
      </div>
    </div>
  </div>
</div>
@endsection

