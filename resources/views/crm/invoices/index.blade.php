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
                  <p class="text-sm mb-0 text-capitalize font-weight-bold">Nezaplacené faktury</p>
                  <h5 class="font-weight-bolder mb-0">{{ number_format($totalOutstanding, 0, ',', ' ') }} CZK</h5>
                </div>
              </div>
              <div class="col-4 text-end">
                <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                  <i class="fas fa-exclamation-triangle text-lg opacity-10" aria-hidden="true"></i>
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
                  <p class="text-sm mb-0 text-capitalize font-weight-bold">Zaplaceno tento měsíc</p>
                  <h5 class="font-weight-bolder mb-0">{{ number_format($totalPaidMonth, 0, ',', ' ') }} CZK</h5>
                </div>
              </div>
              <div class="col-4 text-end">
                <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md">
                  <i class="fas fa-check-circle text-lg opacity-10" aria-hidden="true"></i>
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
          <h5 class="mb-0">Faktury</h5>
          @can('create', \App\Models\Invoice::class)
            <a href="{{ route('invoices.create') }}" class="btn bg-gradient-primary btn-sm">+ Nová faktura</a>
          @endcan
        </div>
        <div class="d-flex gap-2 mt-3 mb-0 flex-wrap">
          <form method="GET" action="{{ route('invoices.index') }}" class="d-flex gap-2">
            <div class="input-group input-group-sm" style="max-width:260px">
              <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Číslo / klient">
              <button class="btn btn-outline-secondary mb-0" type="submit">Hledat</button>
            </div>
            <select name="status" class="form-select form-select-sm" style="max-width:150px" onchange="this.form.submit()">
              <option value="">Všechny stavy</option>
              @php $invStatusLabels = ['draft'=>'Koncept','sent'=>'Odesláno','paid'=>'Zaplaceno','overdue'=>'Po splatnosti','cancelled'=>'Zrušeno']; @endphp
              @foreach(\App\Models\Invoice::STATUSES as $s)
                <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ $invStatusLabels[$s] ?? ucfirst($s) }}</option>
              @endforeach
            </select>
            @if(request('q') || request('status'))
              <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary btn-sm">×</a>
            @endif
          </form>
        </div>
      </div>
      <div class="card-body px-0 pt-0 pb-2">
        <div class="table-responsive p-0">
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Číslo</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Klient</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Stav</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Celkem</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Splatnost</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Akce</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($invoices as $inv)
              @php
                $statusColors = ['draft'=>'secondary','sent'=>'info','paid'=>'success','overdue'=>'danger','cancelled'=>'dark'];
              @endphp
              <tr>
                <td>
                  <div class="d-flex px-2 py-1">
                    <div class="d-flex flex-column justify-content-center">
                      <h6 class="mb-0 text-sm">
                        <a href="{{ route('invoices.show', $inv) }}" class="text-dark">{{ $inv->invoice_number }}</a>
                      </h6>
                    </div>
                  </div>
                </td>
                <td><p class="text-xs font-weight-bold mb-0">{{ $inv->client->name }}</p></td>
                <td class="text-center">
                  @php $invStatusLabels = ['draft'=>'Koncept','sent'=>'Odesláno','paid'=>'Zaplaceno','overdue'=>'Po splatnosti','cancelled'=>'Zrušeno']; @endphp
                  <span class="badge badge-sm bg-gradient-{{ $statusColors[$inv->status] ?? 'secondary' }}">{{ $invStatusLabels[$inv->status] ?? $inv->status }}</span>
                </td>
                <td class="text-center">
                  <span class="text-sm font-weight-bold">{{ number_format($inv->total, 2, ',', ' ') }} {{ $inv->currency }}</span>
                </td>
                <td class="text-center">
                  <span class="text-xs {{ $inv->isOverdue() ? 'text-danger font-weight-bold' : '' }}">
                    {{ $inv->due_at?->format('d.m.Y') ?? '—' }}
                  </span>
                </td>
                <td class="text-center">
                  <a href="{{ route('invoices.show', $inv) }}" class="text-secondary font-weight-bold text-xs me-2">Detail</a>
                  @can('update', $inv)
                    <a href="{{ route('invoices.edit', $inv) }}" class="text-secondary font-weight-bold text-xs me-2">Upravit</a>
                  @endcan
                  @can('delete', $inv)
                    <form method="POST" action="{{ route('invoices.destroy', $inv) }}" class="d-inline" onsubmit="return confirm('Smazat fakturu?')">
                      @csrf @method('DELETE')
                      <button class="btn btn-link text-danger font-weight-bold text-xs p-0 m-0">Smazat</button>
                    </form>
                  @endcan
                </td>
              </tr>
              @empty
              <tr><td colspan="6" class="text-center py-3 text-sm text-secondary">Žádné faktury.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <div class="px-4 pt-3">{{ $invoices->links() }}</div>
      </div>
    </div>
  </div>
</div>
@endsection

