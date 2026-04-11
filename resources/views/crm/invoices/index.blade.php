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

    <div class="card mb-4">
      <div class="card-header pb-3 pt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="mb-0">
            <i class="fas fa-file-invoice me-2 text-primary opacity-75"></i>Faktury
          </h5>
          @can('create', \App\Models\Invoice::class)
            <a href="{{ route('invoices.create') }}" class="btn bg-gradient-primary btn-sm">
              <i class="fas fa-plus me-1"></i> Nová faktura
            </a>
          @endcan
        </div>
        
        {{-- Filters --}}
        <form method="GET" action="{{ route('invoices.index') }}" class="row g-2">
          <div class="col-auto flex-grow-1" style="max-width: 300px;">
            <input type="text" name="q" value="{{ request('q') }}" 
                   class="form-control form-control-sm" placeholder="Číslo / klient…">
          </div>
          <div class="col-auto">
            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
              <option value="">— Všechny stavy —</option>
              @php $invStatusLabels = ['draft'=>'Koncept','sent'=>'Odesláno','paid'=>'Zaplaceno','overdue'=>'Po splatnosti','cancelled'=>'Zrušeno']; @endphp
              @foreach(\App\Models\Invoice::STATUSES as $s)
                <option value="{{ $s }}" @selected(request('status') === $s)>{{ $invStatusLabels[$s] ?? ucfirst($s) }}</option>
              @endforeach
            </select>
          </div>
          @if(request('q') || request('status'))
            <div class="col-auto">
              <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary btn-sm">
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
                <th class="text-xs fw-bold text-secondary px-4 py-3">Číslo</th>
                <th class="text-xs fw-bold text-secondary px-4 py-3">Klient</th>
                <th class="text-xs fw-bold text-secondary text-center px-4 py-3">Stav</th>
                <th class="text-xs fw-bold text-secondary text-center px-4 py-3">Celkem</th>
                <th class="text-xs fw-bold text-secondary text-center px-4 py-3">Splatnost</th>
                <th class="text-xs fw-bold text-secondary text-center px-4 py-3" style="width: 100px;">Akce</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($invoices as $inv)
              @php
                $statusColors = ['draft'=>'secondary','sent'=>'info','paid'=>'success','overdue'=>'danger','cancelled'=>'dark'];
              @endphp
              <tr class="align-middle">
                <td class="px-4 py-3">
                  <a href="{{ route('invoices.show', $inv) }}" class="text-dark fw-500">
                    {{ $inv->invoice_number }}
                  </a>
                </td>
                <td class="px-4 py-3">
                  <small class="text-secondary">{{ $inv->client->name }}</small>
                </td>
                <td class="px-4 py-3 text-center">
                  <span class="badge bg-gradient-{{ $statusColors[$inv->status] ?? 'secondary' }} px-3">
                    <i class="fas fa-file-alt fa-xs me-1 opacity-75"></i>
                    {{ $invStatusLabels[$inv->status] ?? $inv->status }}
                  </span>
                </td>
                <td class="px-4 py-3 text-center">
                  <small class="fw-500">{{ number_format($inv->total, 2, ',', ' ') }} {{ $inv->currency }}</small>
                </td>
                <td class="px-4 py-3 text-center">
                  <small class="@if($inv->isOverdue()) text-danger fw-bold @else text-secondary @endif">
                    <i class="fas fa-calendar fa-xs me-1 opacity-75"></i>
                    {{ $inv->due_at?->format('d.m.Y') ?? '—' }}
                  </small>
                </td>
                <td class="px-4 py-3 text-center">
                  <div class="btn-group btn-group-sm" role="group">
                    <a href="{{ route('invoices.show', $inv) }}" class="btn btn-outline-secondary btn-sm" title="Detail">
                      <i class="fas fa-eye fa-sm"></i>
                    </a>
                    @can('update', $inv)
                      <a href="{{ route('invoices.edit', $inv) }}" class="btn btn-outline-secondary btn-sm" title="Upravit">
                        <i class="fas fa-edit fa-sm"></i>
                      </a>
                    @endcan
                    @can('delete', $inv)
                      <form method="POST" action="{{ route('invoices.destroy', $inv) }}" class="d-inline"
                            onsubmit="return confirm('Smazat fakturu?')">
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
                    <small>Žádné faktury.</small>
                  </div>
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        
        @if($invoices->hasPages())
          <div class="card-footer bg-white px-4 py-3 border-top">
            {{ $invoices->links() }}
          </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection

