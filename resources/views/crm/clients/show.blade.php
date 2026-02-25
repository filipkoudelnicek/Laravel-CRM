@extends('layouts.user_type.auth')

@section('content')
<div class="row">
  {{-- Client info --}}
  <div class="col-lg-4">
    <div class="card mb-4">
      <div class="card-header pb-0 d-flex justify-content-between align-items-center">
        <h5 class="mb-0">{{ $client->name }}</h5>
        @can('update', $client)
          <a href="{{ route('clients.edit', $client) }}" class="btn btn-outline-secondary btn-sm">Upravit</a>
        @endcan
      </div>
      <div class="card-body pt-2">
        <ul class="list-group list-group-flush">
          @if($client->company)
            <li class="list-group-item ps-0 border-0">
              <small class="text-secondary">Firma</small><br>
              <strong>{{ $client->company }}</strong>
            </li>
          @endif
          @if($client->email)
            <li class="list-group-item ps-0 border-0">
              <small class="text-secondary">E-mail</small><br>
              <a href="mailto:{{ $client->email }}">{{ $client->email }}</a>
            </li>
          @endif
          @if($client->phone)
            <li class="list-group-item ps-0 border-0">
              <small class="text-secondary">Telefon</small><br>
              {{ $client->phone }}
            </li>
          @endif
          @if($client->address)
            <li class="list-group-item ps-0 border-0">
              <small class="text-secondary">Adresa</small><br>
              {{ $client->address }}
            </li>
          @endif
          @if($client->notes)
            <li class="list-group-item ps-0 border-0">
              <small class="text-secondary">Poznámky</small><br>
              {{ $client->notes }}
            </li>
          @endif
        </ul>
      </div>
    </div>
  </div>

  {{-- Finance Summary --}}
  <div class="col-lg-8">
    <div class="row mb-4">
      <div class="col-md-4">
        <div class="card">
          <div class="card-body p-3 text-center">
            <p class="text-sm mb-0 text-secondary">Celkem fakturováno</p>
            <h5 class="mb-0">{{ number_format($client->totalInvoiced(), 0, ',', ' ') }} Kč</h5>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card">
          <div class="card-body p-3 text-center">
            <p class="text-sm mb-0 text-secondary">Zaplaceno</p>
            <h5 class="mb-0 text-success">{{ number_format($client->totalPaid(), 0, ',', ' ') }} Kč</h5>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card">
          <div class="card-body p-3 text-center">
            <p class="text-sm mb-0 text-secondary">Nedoplaceno</p>
            <h5 class="mb-0 {{ $client->totalOutstanding() > 0 ? 'text-danger' : '' }}">{{ number_format($client->totalOutstanding(), 0, ',', ' ') }} Kč</h5>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  {{-- Projects --}}
  <div class="col-lg-8">
    <div class="card mb-4">
      <div class="card-header pb-0 d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Projekty ({{ $client->projects->count() }})</h6>
        @can('create', \App\Models\Project::class)
          <a href="{{ route('projects.create') }}?client_id={{ $client->id }}" class="btn bg-gradient-primary btn-sm">+ Nový projekt</a>
        @endcan
      </div>
      <div class="card-body px-0 pt-0 pb-2">
        <div class="table-responsive p-0">
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Projekt</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Stav</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Úkoly</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Termín</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($client->projects as $project)
              <tr>
                <td>
                  <a href="{{ route('projects.show', $project) }}" class="text-sm font-weight-bold text-dark ms-2">
                    {{ $project->name }}
                  </a>
                </td>
                <td class="text-center">
                  @php
                    $colors = ['planned'=>'secondary','active'=>'success','on_hold'=>'warning','done'=>'info'];
                    $statusLabels = ['planned'=>'Plánovaný','active'=>'Aktivní','on_hold'=>'Pozastavený','done'=>'Dokončený'];
                  @endphp
                  <span class="badge badge-sm bg-gradient-{{ $colors[$project->status] ?? 'secondary' }}">
                    {{ $statusLabels[$project->status] ?? $project->status }}
                  </span>
                </td>
                <td class="text-center"><span class="text-xs">{{ $project->tasks_count }}</span></td>
                <td class="text-center"><span class="text-xs">{{ $project->due_date?->format('d.m.Y') ?? '—' }}</span></td>
              </tr>
              @empty
              <tr><td colspan="4" class="text-center py-3 text-sm text-secondary">Žádné projekty.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
{{-- Recent Invoices --}}
<div class="row">
  <div class="col-lg-8">
    <div class="card mb-4">
      <div class="card-header pb-0 d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Poslední faktury</h6>
        <a href="{{ route('invoices.create') }}?client_id={{ $client->id }}" class="btn bg-gradient-primary btn-sm">+ Nová faktura</a>
      </div>
      <div class="card-body px-0 pt-0 pb-2">
        <div class="table-responsive p-0">
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Číslo</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Stav</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Částka</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Splatnost</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($client->invoices()->latest()->take(5)->get() as $inv)
              @php $sc = ['draft'=>'secondary','sent'=>'info','paid'=>'success','overdue'=>'danger','cancelled'=>'dark']; @endphp
              <tr>
                <td><a href="{{ route('invoices.show', $inv) }}" class="text-sm font-weight-bold text-dark ms-2">{{ $inv->invoice_number }}</a></td>
                <td class="text-center"><span class="badge badge-sm bg-gradient-{{ $sc[$inv->status] ?? 'secondary' }}">{{ $inv->status }}</span></td>
                <td class="text-center text-sm">{{ number_format($inv->total, 0, ',', ' ') }} {{ $inv->currency }}</td>
                <td class="text-center text-xs">{{ $inv->due_at?->format('d.m.Y') ?? '—' }}</td>
              </tr>
              @empty
              <tr><td colspan="4" class="text-center py-3 text-sm text-secondary">Zatím žádné faktury.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  {{-- Support Plans --}}
  <div class="col-lg-4">
    <div class="card mb-4">
      <div class="card-header pb-0 d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Podpůrné plány</h6>
        <a href="{{ route('support-plans.create') }}?client_id={{ $client->id }}" class="btn bg-gradient-primary btn-sm">+ Nový</a>
      </div>
      <div class="card-body pt-2">
        @forelse ($client->supportPlans()->active()->get() as $sp)
        <div class="d-flex justify-content-between align-items-center mb-2">
          <div>
            <a href="{{ route('support-plans.show', $sp) }}" class="text-sm font-weight-bold">{{ $sp->title }}</a>
            <p class="text-xs text-secondary mb-0">{{ $sp->period_from->format('d.m.Y') }} – {{ $sp->period_to->format('d.m.Y') }}</p>
          </div>
          <span class="text-sm font-weight-bold">{{ number_format($sp->price, 0, ',', ' ') }} {{ $sp->currency }}</span>
        </div>
        @empty
        <p class="text-sm text-secondary text-center py-2">Žádné aktivní plány.</p>
        @endforelse
      </div>
    </div>
  </div>
</div>
@endsection

