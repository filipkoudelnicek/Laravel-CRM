@extends('layouts.user_type.auth')

@section('content')
<div class="row">
  <div class="col-lg-8">
    <div class="card mb-4">
      <div class="card-header pb-0 d-flex justify-content-between align-items-center">
        <div>
          <h5 class="mb-0">{{ $supportPlan->title }}</h5>
          @php $statusColors = ['active'=>'success','expired'=>'secondary','cancelled'=>'dark']; @endphp
          @php $spStatusLabels = ['active'=>'Aktivní','expired'=>'Vypršelo','cancelled'=>'Zrušeno']; @endphp
          <span class="badge bg-gradient-{{ $statusColors[$supportPlan->status] ?? 'secondary' }} mt-1">{{ $spStatusLabels[$supportPlan->status] ?? ucfirst($supportPlan->status) }}</span>
          @if($supportPlan->isExpiringSoon())
            <span class="badge bg-gradient-warning mt-1"><i class="fas fa-exclamation-circle me-1"></i>Expiruje brzy</span>
          @endif
        </div>
        <div class="d-flex gap-2">
          @can('update', $supportPlan)
            <a href="{{ route('support-plans.edit', $supportPlan) }}" class="btn btn-outline-secondary btn-sm">Upravit</a>
          @endcan
          @can('delete', $supportPlan)
            <form method="POST" action="{{ route('support-plans.destroy', $supportPlan) }}" onsubmit="return confirm('Smazat plán?')">
              @csrf @method('DELETE')
              <button class="btn btn-outline-danger btn-sm">Smazat</button>
            </form>
          @endcan
        </div>
      </div>
      <div class="card-body pt-2">
        <ul class="list-group list-group-flush">
          <li class="list-group-item ps-0 border-0">
            <small class="text-secondary">Klient</small><br>
            <a href="{{ route('clients.show', $supportPlan->client) }}" class="font-weight-bold text-dark">{{ $supportPlan->client->name }}</a>
          </li>
          <li class="list-group-item ps-0 border-0">
            <small class="text-secondary">Cena</small><br>
            <strong>{{ number_format($supportPlan->price, 2, ',', ' ') }} {{ $supportPlan->currency }}</strong>
          </li>
          <li class="list-group-item ps-0 border-0">
            <small class="text-secondary">Období</small><br>
            {{ $supportPlan->period_from->format('d.m.Y') }} – {{ $supportPlan->period_to->format('d.m.Y') }}
          </li>
          @if($supportPlan->notes)
          <li class="list-group-item ps-0 border-0">
            <small class="text-secondary">Poznámky</small><br>
            {{ $supportPlan->notes }}
          </li>
          @endif
          <li class="list-group-item ps-0 border-0">
            <small class="text-secondary">Vytvořil</small><br>
            {{ $supportPlan->creator?->name ?? '—' }}
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>
@endsection

