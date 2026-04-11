@extends('layouts.user_type.auth')

@section('content')
<div class="row">
  <div class="col-12">
    {{-- Summary cards --}}
    <div class="row mb-4">
      <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
        <div class="card">
          <div class="card-body p-3">
            <div class="row">
              <div class="col-8">
                <div class="numbers">
                  <p class="text-sm mb-0 text-capitalize font-weight-bold">Aktivní podpory</p>
                  <h5 class="font-weight-bolder mb-0">{{ number_format($activeTotal, 0, ',', ' ') }} CZK</h5>
                </div>
              </div>
              <div class="col-4 text-end">
                <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md">
                  <i class="fas fa-shield-alt text-lg opacity-10" aria-hidden="true"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
        <div class="card">
          <div class="card-body p-3">
            <div class="row">
              <div class="col-8">
                <div class="numbers">
                  <p class="text-sm mb-0 text-capitalize font-weight-bold">Expirují do 30 dní</p>
                  <h5 class="font-weight-bolder mb-0">{{ $expiringSoon }} <span class="text-sm text-warning">({{ number_format($expiringAmount, 0, ',', ' ') }} CZK)</span></h5>
                </div>
              </div>
              <div class="col-4 text-end">
                <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                  <i class="fas fa-clock text-lg opacity-10" aria-hidden="true"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="card mb-4">
      <div class="card-header pb-3 pt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="mb-0">
            <i class="fas fa-shield-alt me-2 text-primary opacity-75"></i>Podpora / Předplatné
          </h5>
          @can('create', \App\Models\SupportPlan::class)
            <a href="{{ route('support-plans.create') }}" class="btn bg-gradient-primary btn-sm">
              <i class="fas fa-plus me-1"></i> Nová podpora
            </a>
          @endcan
        </div>
        
        {{-- Filters --}}
        <form method="GET" action="{{ route('support-plans.index') }}" class="row g-2">
          <div class="col-auto flex-grow-1" style="max-width: 300px;">
            <input type="text" name="q" value="{{ request('q') }}" 
                   class="form-control form-control-sm" placeholder="Název / klient…">
          </div>
          <div class="col-auto">
            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
              <option value="">— Všechny stavy —</option>
              @php $spStatusLabels = ['active'=>'Aktivní','expired'=>'Vypršelo','cancelled'=>'Zrušeno']; @endphp
              @foreach(\App\Models\SupportPlan::STATUSES as $s)
                <option value="{{ $s }}" @selected(request('status') === $s)>{{ $spStatusLabels[$s] ?? ucfirst($s) }}</option>
              @endforeach
            </select>
          </div>
          @if(request('q') || request('status'))
            <div class="col-auto">
              <a href="{{ route('support-plans.index') }}" class="btn btn-outline-secondary btn-sm">
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
                <th class="text-xs fw-bold text-secondary px-4 py-3">Název</th>
                <th class="text-xs fw-bold text-secondary px-4 py-3">Klient</th>
                <th class="text-xs fw-bold text-secondary text-center px-4 py-3">Stav</th>
                <th class="text-xs fw-bold text-secondary text-center px-4 py-3">Cena</th>
                <th class="text-xs fw-bold text-secondary text-center px-4 py-3">Období</th>
                <th class="text-xs fw-bold text-secondary text-center px-4 py-3" style="width: 100px;">Akce</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($plans as $plan)
              @php
                $statusColors = ['active'=>'success','expired'=>'secondary','cancelled'=>'dark'];
                $isExpiring = $plan->isExpiringSoon();
              @endphp
              <tr class="align-middle">
                <td class="px-4 py-3">
                  <a href="{{ route('support-plans.show', $plan) }}" class="text-dark fw-500 text-decoration-none">
                    {{ $plan->title }}
                  </a>
                </td>
                <td class="px-4 py-3">
                  <small class="text-secondary">{{ $plan->client->name }}</small>
                </td>
                <td class="px-4 py-3 text-center">
                  <span class="badge bg-gradient-{{ $statusColors[$plan->status] ?? 'secondary' }} px-3">
                    <i class="fas fa-shield-alt fa-xs me-1 opacity-75"></i>
                    {{ $spStatusLabels[$plan->status] ?? $plan->status }}
                    @if($isExpiring)
                      <i class="fas fa-exclamation-circle ms-1 fa-xs"></i>
                    @endif
                  </span>
                </td>
                <td class="px-4 py-3 text-center">
                  <small class="fw-500">{{ number_format($plan->price, 0, ',', ' ') }} {{ $plan->currency }}</small>
                </td>
                <td class="px-4 py-3 text-center">
                  <small class="text-secondary">
                    <i class="fas fa-calendar fa-xs me-1 opacity-75"></i>
                    {{ $plan->period_from->format('d.m.Y') }} – {{ $plan->period_to->format('d.m.Y') }}
                  </small>
                </td>
                <td class="px-4 py-3 text-center">
                  <div class="btn-group btn-group-sm" role="group">
                    <a href="{{ route('support-plans.show', $plan) }}" class="btn btn-outline-secondary btn-sm" title="Detail">
                      <i class="fas fa-eye fa-sm"></i>
                    </a>
                    @can('update', $plan)
                      <a href="{{ route('support-plans.edit', $plan) }}" class="btn btn-outline-secondary btn-sm" title="Upravit">
                        <i class="fas fa-edit fa-sm"></i>
                      </a>
                    @endcan
                    @can('delete', $plan)
                      <form method="POST" action="{{ route('support-plans.destroy', $plan) }}" class="d-inline"
                            onsubmit="return confirm('Smazat podporu?')">
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
                    <small>Žádné plány podpory.</small>
                  </div>
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        
        @if($plans->hasPages())
          <div class="card-footer bg-white px-4 py-3 border-top">
            {{ $plans->links() }}
          </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection

