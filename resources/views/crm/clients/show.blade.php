@extends('layouts.user_type.auth')

@section('content')

{{-- Header --}}
<div class="row mb-4">
  <div class="col-12">
    <div class="d-flex justify-content-between align-items-start">
      <div>
        <h3 class="mb-1">
          <i class="fas fa-building me-2 text-primary opacity-75"></i>{{ $client->name }}
        </h3>
        @if($client->company)
          <p class="text-secondary mb-0">{{ $client->company }}</p>
        @endif
      </div>
      <div class="btn-group btn-group-sm" role="group">
        @can('update', $client)
          <a href="{{ route('clients.edit', $client) }}" class="btn btn-outline-secondary">
            <i class="fas fa-edit me-1"></i> Upravit
          </a>
        @endcan
        @can('delete', $client)
          <form method="POST" action="{{ route('clients.destroy', $client) }}" class="d-inline" onsubmit="return confirm('Smazat klienta?')">
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
  {{-- Client Info --}}
  <div class="col-lg-4">
    <div class="card mb-4">
      <div class="card-header pb-3 pt-4">
        <h6 class="mb-0">
          <i class="fas fa-info-circle me-2 text-primary opacity-75"></i>Informace
        </h6>
      </div>
      <div class="card-body">
        @if($client->email)
          <div class="mb-4">
            <small class="text-secondary d-block mb-2">
              <i class="fas fa-envelope fa-xs me-1 opacity-75"></i>E-MAIL
            </small>
            <a href="mailto:{{ $client->email }}" class="text-sm fw-500">
              {{ $client->email }}
            </a>
          </div>
        @endif

        @if($client->phone)
          <div class="mb-4">
            <small class="text-secondary d-block mb-2">
              <i class="fas fa-phone fa-xs me-1 opacity-75"></i>TELEFON
            </small>
            <a href="tel:{{ $client->phone }}" class="text-sm fw-500">
              {{ $client->phone }}
            </a>
          </div>
        @endif

        @if($client->address)
          <div class="mb-4">
            <small class="text-secondary d-block mb-2">
              <i class="fas fa-map-marker-alt fa-xs me-1 opacity-75"></i>ADRESA
            </small>
            <span class="text-sm">{{ $client->address }}</span>
          </div>
        @endif

        @if($client->notes)
          <hr class="my-3">
          <div class="mb-0">
            <small class="text-secondary d-block mb-2">
              <i class="fas fa-sticky-note fa-xs me-1 opacity-75"></i>POZNÁMKY
            </small>
            <span class="text-sm">{{ $client->notes }}</span>
          </div>
        @endif
      </div>
    </div>

    {{-- Finance Summary --}}
    <div class="card mb-4">
      <div class="card-header pb-3 pt-4">
        <h6 class="mb-0">
          <i class="fas fa-chart-line me-2 text-primary opacity-75"></i>Finance
        </h6>
      </div>
      <div class="card-body">
        <div class="mb-4">
          <small class="text-secondary d-block mb-2">
            <i class="fas fa-receipt fa-xs me-1 opacity-75"></i>CELKEM FAKTUROVÁNO
          </small>
          <h6 class="mb-0 fw-bold">{{ number_format($finance['totalInvoiced'], 0, ',', ' ') }} Kč</h6>
        </div>

        <div class="mb-4">
          <small class="text-secondary d-block mb-2">
            <i class="fas fa-check-circle fa-xs me-1 opacity-75"></i>ZAPLACENO
          </small>
          <h6 class="mb-0 fw-bold text-success">{{ number_format($finance['totalPaid'], 0, ',', ' ') }} Kč</h6>
        </div>

        <div class="mb-0">
          <small class="text-secondary d-block mb-2">
            <i class="fas fa-exclamation-circle fa-xs me-1 opacity-75"></i>NEDOPLACENO
          </small>
          <h6 class="mb-0 fw-bold {{ $finance['totalOutstanding'] > 0 ? 'text-danger' : '' }}">
            {{ number_format($finance['totalOutstanding'], 0, ',', ' ') }} Kč
          </h6>
        </div>
      </div>
    </div>
  </div>

  {{-- Projects & Invoices --}}
  <div class="col-lg-8">
    {{-- Projects --}}
    <div class="card mb-4">
      <div class="card-header pb-0 d-flex justify-content-between align-items-center">
        <h6 class="mb-0">
          <i class="fas fa-folder me-2 opacity-75"></i>Projekty (<span class="badge bg-light text-dark">{{ $client->projects->count() }}</span>)
        </h6>
        @can('create', \App\Models\Project::class)
          <a href="{{ route('projects.create') }}?client_id={{ $client->id }}" class="btn btn-outline-primary btn-sm">
            <i class="fas fa-plus me-1"></i>Nový projekt
          </a>
        @endcan
      </div>
      <div class="card-body px-0 pt-0 pb-2">
        @if($client->projects->count())
        <div class="table-responsive p-0">
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Projekt</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Stav</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Úkoly</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Termín</th>
              </tr>
            </thead>
            <tbody>
              @php
                $colors = ['planned'=>'secondary','active'=>'success','on_hold'=>'warning','done'=>'info'];
                $statusLabels = ['planned'=>'Plánovaný','active'=>'Aktivní','on_hold'=>'Pozastavený','done'=>'Dokončený'];
              @endphp
              @foreach($client->projects as $project)
              <tr class="align-middle">
                <td class="ps-3">
                  <a href="{{ route('projects.show', $project) }}" class="text-sm fw-bold text-dark">
                    {{ Str::limit($project->name, 40) }}
                  </a>
                </td>
                <td class="text-center">
                  <span class="badge badge-sm bg-gradient-{{ $colors[$project->status] ?? 'secondary' }}">
                    {{ $statusLabels[$project->status] ?? $project->status }}
                  </span>
                </td>
                <td class="text-center"><span class="text-xs">{{ $project->tasks_count ?? $project->tasks->count() }}</span></td>
                <td class="text-center"><span class="text-xs">{{ $project->due_date?->format('d.m.Y') ?? '—' }}</span></td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        @else
        <div class="text-center py-4">
          <p class="text-sm text-secondary">Žádné projekty. <a href="{{ route('projects.create') }}?client_id={{ $client->id }}">Vytvořit nový</a></p>
        </div>
        @endif
      </div>
    </div>

    {{-- Recent Invoices --}}
    <div class="card mb-4">
      <div class="card-header pb-0 d-flex justify-content-between align-items-center">
        <h6 class="mb-0">
          <i class="fas fa-receipt me-2 opacity-75"></i>Poslední faktury
        </h6>
        <a href="{{ route('invoices.create') }}?client_id={{ $client->id }}" class="btn btn-outline-primary btn-sm">
          <i class="fas fa-plus me-1"></i>Nová faktura
        </a>
      </div>
      <div class="card-body px-0 pt-0 pb-2">
        @if($client->invoices->count())
        <div class="table-responsive p-0">
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Číslo</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Stav</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Částka</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Splatnost</th>
              </tr>
            </thead>
            <tbody>
              @php $sc = ['draft'=>'secondary','sent'=>'info','paid'=>'success','overdue'=>'danger','cancelled'=>'dark']; @endphp
              @foreach($client->invoices as $inv)
              <tr class="align-middle">
                <td class="ps-3">
                  <a href="{{ route('invoices.show', $inv) }}" class="text-sm fw-bold text-dark">{{ $inv->invoice_number }}</a>
                </td>
                <td class="text-center">
                  <span class="badge badge-sm bg-gradient-{{ $sc[$inv->status] ?? 'secondary' }}">
                    {{ ['draft'=>'Koncept','sent'=>'Odeslána','paid'=>'Zaplacena','overdue'=>'Po splatnosti','cancelled'=>'Zrušena'][$inv->status] ?? $inv->status }}
                  </span>
                </td>
                <td class="text-center text-sm">{{ number_format($inv->total, 0, ',', ' ') }} {{ $inv->currency }}</td>
                <td class="text-center text-xs">{{ $inv->due_at?->format('d.m.Y') ?? '—' }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        @else
        <div class="text-center py-4">
          <p class="text-sm text-secondary">Zatím žádné faktury. <a href="{{ route('invoices.create') }}?client_id={{ $client->id }}">Vytvořit novou</a></p>
        </div>
        @endif
      </div>
    </div>

    {{-- Support Plans --}}
    <div class="card mb-4">
      <div class="card-header pb-0 d-flex justify-content-between align-items-center">
        <h6 class="mb-0">
          <i class="fas fa-shield-alt me-2 opacity-75"></i>Podpůrné plány
        </h6>
        <a href="{{ route('support-plans.create') }}?client_id={{ $client->id }}" class="btn btn-outline-primary btn-sm">
          <i class="fas fa-plus me-1"></i>Nový plán
        </a>
      </div>
      <div class="card-body">
        @forelse($client->supportPlans as $sp)
          <div class="d-flex justify-content-between align-items-center mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
            <div>
              <a href="{{ route('support-plans.show', $sp) }}" class="text-sm fw-bold">{{ $sp->title }}</a>
              <p class="text-xs text-secondary mb-0">
                <i class="fas fa-calendar-alt fa-xs me-1 opacity-75"></i>{{ $sp->period_from->format('d.m.Y') }} – {{ $sp->period_to->format('d.m.Y') }}
              </p>
            </div>
            <span class="text-sm fw-bold text-info">{{ number_format($sp->price, 0, ',', ' ') }} Kč</span>
          </div>
        @empty
          <p class="text-sm text-secondary text-center py-2">Žádné aktivní plány.</p>
        @endforelse
      </div>
    </div>
  </div>
</div>
@endsection

