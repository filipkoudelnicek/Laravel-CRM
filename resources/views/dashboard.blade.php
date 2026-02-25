@extends('layouts.user_type.auth')

@section('content')

{{-- Filters --}}
<div class="row mb-3">
  <div class="col-12">
    <form method="GET" class="d-flex gap-2 align-items-end flex-wrap">
      <div>
        <label class="form-label text-xs mb-1">Období (měsíce)</label>
        <select name="months" class="form-select form-select-sm" style="width:100px" onchange="this.form.submit()">
          @foreach([6,12,24] as $m)
            <option value="{{ $m }}" @selected($months == $m)>{{ $m }}</option>
          @endforeach
        </select>
      </div>
      @if($allCurrencies->count() > 1)
      <div>
        <label class="form-label text-xs mb-1">Měna</label>
        <select name="currency" class="form-select form-select-sm" style="width:100px" onchange="this.form.submit()">
          <option value="">Vše</option>
          @foreach($allCurrencies as $c)
            <option value="{{ $c }}" @selected($currency === $c)>{{ $c }}</option>
          @endforeach
        </select>
      </div>
      @endif
    </form>
  </div>
</div>

{{-- KPI Cards --}}
<div class="row">
  <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
    <div class="card">
      <div class="card-body p-3">
        <div class="row">
          <div class="col-8">
            <div class="numbers">
              <p class="text-sm mb-0 text-capitalize font-weight-bold">Nedoplaceno</p>
              <h5 class="font-weight-bolder mb-0">{{ number_format($totalOutstanding, 0, ',', ' ') }} Kč</h5>
            </div>
          </div>
          <div class="col-4 text-end">
            <div class="icon icon-shape bg-gradient-danger shadow text-center border-radius-md">
              <i class="fas fa-file-invoice-dollar text-lg opacity-10"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
    <div class="card">
      <div class="card-body p-3">
        <div class="row">
          <div class="col-8">
            <div class="numbers">
              <p class="text-sm mb-0 text-capitalize font-weight-bold">Aktivní projekty</p>
              <h5 class="font-weight-bolder mb-0">{{ $activeProjects }}</h5>
            </div>
          </div>
          <div class="col-4 text-end">
            <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
              <i class="fas fa-project-diagram text-lg opacity-10"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
    <div class="card">
      <div class="card-body p-3">
        <div class="row">
          <div class="col-8">
            <div class="numbers">
              <p class="text-sm mb-0 text-capitalize font-weight-bold">Aktivní podpora</p>
              <h5 class="font-weight-bolder mb-0">{{ number_format($activeSupportTotal, 0, ',', ' ') }} Kč</h5>
            </div>
          </div>
          <div class="col-4 text-end">
            <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md">
              <i class="fas fa-life-ring text-lg opacity-10"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-sm-6">
    <div class="card">
      <div class="card-body p-3">
        <div class="row">
          <div class="col-8">
            <div class="numbers">
              <p class="text-sm mb-0 text-capitalize font-weight-bold">Brzy expiruje</p>
              <h5 class="font-weight-bolder mb-0">
                {{ $expiringSoonCount }}
                @if($expiringSoonAmount > 0)
                  <span class="text-warning text-sm font-weight-bolder">{{ number_format($expiringSoonAmount, 0, ',', ' ') }} Kč</span>
                @endif
              </h5>
            </div>
          </div>
          <div class="col-4 text-end">
            <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
              <i class="fas fa-exclamation-triangle text-lg opacity-10"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Charts Row --}}
<div class="row mt-4">
  <div class="col-lg-7 mb-lg-0 mb-4">
    <div class="card z-index-2">
      <div class="card-header pb-0">
        <h6>Měsíční příjmy</h6>
        <p class="text-sm">Zaplacené faktury za posledních {{ $months }} měsíců</p>
      </div>
      <div class="card-body p-3">
        <div class="chart">
          <canvas id="chart-revenue" class="chart-canvas" height="300"></canvas>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-5">
    <div class="card z-index-2 h-100">
      <div class="card-header pb-0">
        <h6>Úkoly podle stavu</h6>
        <p class="text-sm">Aktivní projekty</p>
      </div>
      <div class="card-body p-3 d-flex align-items-center justify-content-center">
        <div class="chart" style="max-height:260px">
          <canvas id="chart-tasks" class="chart-canvas" height="260"></canvas>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Recent Invoices & Support --}}
<div class="row my-4">
  <div class="col-lg-8 col-md-6 mb-md-0 mb-4">
    <div class="card">
      <div class="card-header pb-0 d-flex justify-content-between align-items-center">
        <div>
          <h6>Poslední faktury</h6>
        </div>
        <a href="{{ route('invoices.index') }}" class="btn btn-outline-primary btn-sm mb-0">Všechny</a>
      </div>
      <div class="card-body px-0 pb-2">
        <div class="table-responsive">
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Číslo</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Klient</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Stav</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Částka</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Splatnost</th>
              </tr>
            </thead>
            <tbody>
              @forelse($recentInvoices as $inv)
              @php $sc = ['draft'=>'secondary','sent'=>'info','paid'=>'success','overdue'=>'danger','cancelled'=>'dark']; @endphp
              <tr>
                <td>
                  <a href="{{ route('invoices.show', $inv) }}" class="text-sm font-weight-bold text-dark ps-2">{{ $inv->invoice_number }}</a>
                </td>
                <td>
                  <span class="text-xs">{{ $inv->client->name ?? '—' }}</span>
                </td>
                <td class="text-center">
                  @php $invLabels = ['draft'=>'Koncept','sent'=>'Odesláno','paid'=>'Zaplaceno','overdue'=>'Po splatnosti','cancelled'=>'Zrušeno']; @endphp
                  <span class="badge badge-sm bg-gradient-{{ $sc[$inv->status] ?? 'secondary' }}">{{ $invLabels[$inv->status] ?? $inv->status }}</span>
                </td>
                <td class="text-center">
                  <span class="text-xs font-weight-bold">{{ number_format($inv->total, 0, ',', ' ') }} {{ $inv->currency }}</span>
                </td>
                <td class="text-center">
                  <span class="text-xs {{ $inv->isOverdue() ? 'text-danger font-weight-bold' : '' }}">{{ $inv->due_at?->format('d.m.Y') ?? '—' }}</span>
                </td>
              </tr>
              @empty
              <tr><td colspan="5" class="text-center py-3 text-sm text-secondary">Žádné faktury.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-4 col-md-6">
    <div class="card h-100">
      <div class="card-header pb-0 d-flex justify-content-between align-items-center">
        <h6>Podpůrné plány</h6>
        <a href="{{ route('support-plans.index') }}" class="btn btn-outline-primary btn-sm mb-0">Vše</a>
      </div>
      <div class="card-body p-3">
        <ul class="list-group list-group-flush">
          <li class="list-group-item d-flex justify-content-between align-items-center px-0">
            <div>
              <p class="text-sm font-weight-bold mb-0">Aktivní plány</p>
              <p class="text-xs text-secondary mb-0">celkem</p>
            </div>
            <span class="badge bg-gradient-success">{{ $activeSupportCount }}</span>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center px-0">
            <div>
              <p class="text-sm font-weight-bold mb-0">Měsíční příjem</p>
              <p class="text-xs text-secondary mb-0">z aktivních plánů</p>
            </div>
            <span class="text-sm font-weight-bold">{{ number_format($activeSupportTotal, 0, ',', ' ') }} Kč</span>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center px-0">
            <div>
              <p class="text-sm font-weight-bold mb-0">Brzy vyprší</p>
              <p class="text-xs text-secondary mb-0">do 30 dnů</p>
            </div>
            <span class="badge bg-gradient-warning">{{ $expiringSoonCount }}</span>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>

@endsection

@push('dashboard')
<script>
window.onload = function() {
  // ── Revenue Bar Chart ──────────────────────────────────────
  var ctxRev = document.getElementById("chart-revenue").getContext("2d");

  var gradientRev = ctxRev.createLinearGradient(0, 230, 0, 50);
  gradientRev.addColorStop(1, 'rgba(94,114,228,0.2)');
  gradientRev.addColorStop(0.2, 'rgba(94,114,228,0.0)');
  gradientRev.addColorStop(0, 'rgba(94,114,228,0)');

  new Chart(ctxRev, {
    type: "bar",
    data: {
      labels: {!! json_encode($allMonths->keys()->map(fn($k) => \Carbon\Carbon::createFromFormat('Y-m', $k)->translatedFormat('M Y'))->values()) !!},
      datasets: [{
        label: "Příjem (Kč)",
        tension: 0.4,
        borderWidth: 0,
        borderRadius: 4,
        borderSkipped: false,
        backgroundColor: "#5e72e4",
        data: {!! json_encode($allMonths->values()) !!},
        maxBarThickness: 20
      }],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        tooltip: {
          callbacks: {
            label: function(ctx) {
              return ctx.parsed.y.toLocaleString('cs-CZ') + ' Kč';
            }
          }
        }
      },
      interaction: { intersect: false, mode: 'index' },
      scales: {
        y: {
          grid: { drawBorder: false, display: true, drawOnChartArea: true, drawTicks: false, borderDash: [5,5] },
          ticks: {
            display: true, padding: 10, color: '#b2b9bf',
            font: { size: 11, family: "Open Sans", style: 'normal', lineHeight: 2 },
            callback: function(v) { return v.toLocaleString('cs-CZ'); }
          }
        },
        x: {
          grid: { drawBorder: false, display: false, drawOnChartArea: false, drawTicks: false },
          ticks: { display: true, color: '#b2b9bf', padding: 10, font: { size: 11, family: "Open Sans" } }
        }
      }
    }
  });

  // ── Tasks Doughnut Chart ───────────────────────────────────
  var ctxTasks = document.getElementById("chart-tasks").getContext("2d");

  @php
    $statusLabels = ['todo' => 'K udělání', 'in_progress' => 'Rozpracováno', 'review' => 'Ke kontrole', 'done' => 'Hotovo'];
    $statusColors = ['todo' => '#8392ab', 'in_progress' => '#5e72e4', 'review' => '#fb6340', 'done' => '#2dce89'];
    $chartLabels = [];
    $chartData = [];
    $chartColors = [];
    foreach ($statusLabels as $key => $label) {
      $chartLabels[] = $label;
      $chartData[] = (int) ($tasksByStatus[$key] ?? 0);
      $chartColors[] = $statusColors[$key];
    }
  @endphp

  new Chart(ctxTasks, {
    type: "doughnut",
    data: {
      labels: {!! json_encode($chartLabels) !!},
      datasets: [{
        data: {!! json_encode($chartData) !!},
        backgroundColor: {!! json_encode($chartColors) !!},
        borderWidth: 2,
        borderColor: '#fff'
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      cutout: '65%',
      plugins: {
        legend: { position: 'bottom', labels: { padding: 15, usePointStyle: true, pointStyleWidth: 10 } }
      }
    }
  });
};
</script>
@endpush


