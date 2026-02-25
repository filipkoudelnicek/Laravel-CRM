@extends('layouts.user_type.auth')

@section('content')
<div class="row">
  <div class="col-12">
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

    <div class="card mb-4 mx-0">
      <div class="card-header pb-0">
        <div class="d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Podpora / Předplatné</h5>
          @can('create', \App\Models\SupportPlan::class)
            <a href="{{ route('support-plans.create') }}" class="btn bg-gradient-primary btn-sm">+ Nová podpora</a>
          @endcan
        </div>
        <div class="d-flex gap-2 mt-3 mb-0 flex-wrap">
          <form method="GET" action="{{ route('support-plans.index') }}" class="d-flex gap-2">
            <div class="input-group input-group-sm" style="max-width:260px">
              <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Název / klient">
              <button class="btn btn-outline-secondary mb-0" type="submit">Hledat</button>
            </div>
            <select name="status" class="form-select form-select-sm" style="max-width:150px" onchange="this.form.submit()">
              <option value="">Všechny stavy</option>
              @php $spStatusLabels = ['active'=>'Aktivní','expired'=>'Vypršelo','cancelled'=>'Zrušeno']; @endphp
              @foreach(\App\Models\SupportPlan::STATUSES as $s)
                <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ $spStatusLabels[$s] ?? ucfirst($s) }}</option>
              @endforeach
            </select>
            @if(request('q') || request('status'))
              <a href="{{ route('support-plans.index') }}" class="btn btn-outline-secondary btn-sm">×</a>
            @endif
          </form>
        </div>
      </div>
      <div class="card-body px-0 pt-0 pb-2">
        <div class="table-responsive p-0">
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Název</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Klient</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Stav</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Cena</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Období</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Akce</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($plans as $plan)
              @php
                $statusColors = ['active'=>'success','expired'=>'secondary','cancelled'=>'dark'];
                $isExpiring = $plan->isExpiringSoon();
              @endphp
              <tr>
                <td>
                  <div class="d-flex px-2 py-1">
                    <div class="d-flex flex-column justify-content-center">
                      <h6 class="mb-0 text-sm">
                        <a href="{{ route('support-plans.show', $plan) }}" class="text-dark">{{ $plan->title }}</a>
                      </h6>
                    </div>
                  </div>
                </td>
                <td><p class="text-xs font-weight-bold mb-0">{{ $plan->client->name }}</p></td>
                <td class="text-center">
                  <span class="badge badge-sm bg-gradient-{{ $statusColors[$plan->status] ?? 'secondary' }}">
                    @php $spStatusLabels = ['active'=>'Aktivní','expired'=>'Vypršelo','cancelled'=>'Zrušeno']; @endphp
                    {{ $spStatusLabels[$plan->status] ?? $plan->status }}
                    @if($isExpiring) <i class="fas fa-exclamation-circle ms-1"></i> @endif
                  </span>
                </td>
                <td class="text-center">
                  <span class="text-sm font-weight-bold">{{ number_format($plan->price, 0, ',', ' ') }} {{ $plan->currency }}</span>
                </td>
                <td class="text-center">
                  <span class="text-xs">{{ $plan->period_from->format('d.m.Y') }} – {{ $plan->period_to->format('d.m.Y') }}</span>
                </td>
                <td class="text-center">
                  <a href="{{ route('support-plans.show', $plan) }}" class="text-secondary font-weight-bold text-xs me-2">Detail</a>
                  @can('update', $plan)
                    <a href="{{ route('support-plans.edit', $plan) }}" class="text-secondary font-weight-bold text-xs me-2">Upravit</a>
                  @endcan
                  @can('delete', $plan)
                    <form method="POST" action="{{ route('support-plans.destroy', $plan) }}" class="d-inline" onsubmit="return confirm('Smazat podporu?')">
                      @csrf @method('DELETE')
                      <button class="btn btn-link text-danger font-weight-bold text-xs p-0 m-0">Smazat</button>
                    </form>
                  @endcan
                </td>
              </tr>
              @empty
              <tr><td colspan="6" class="text-center py-3 text-sm text-secondary">Žádné plány podpory.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <div class="px-4 pt-3">{{ $plans->links() }}</div>
      </div>
    </div>
  </div>
</div>
@endsection

