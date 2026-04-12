@props(['client'])

<div class="modal-header border-0 bg-transparent pb-2">
  <h5 class="modal-title fs-6 fw-bold">{{ $client->name }}</h5>
</div>

<div class="modal-body pb-0">
  {{-- Email & Phone --}}
  <div class="row g-2 mb-4">
    <div class="col-6">
      <small class="text-secondary d-block mb-2"><i class="fas fa-envelope fa-xs me-1"></i>E-MAIL</small>
      @if($client->email)
        <a href="mailto:{{ $client->email }}" class="text-decoration-none text-dark text-sm fw-500">{{ $client->email }}</a>
      @else
        <span class="text-secondary">—</span>
      @endif
    </div>
    <div class="col-6">
      <small class="text-secondary d-block mb-2"><i class="fas fa-phone fa-xs me-1"></i>TELEFON</small>
      @if($client->phone)
        <a href="tel:{{ $client->phone }}" class="text-decoration-none text-dark text-sm fw-500">{{ $client->phone }}</a>
      @else
        <span class="text-secondary">—</span>
      @endif
    </div>
  </div>

  {{-- Company --}}
  @if($client->company)
  <div class="mb-4">
    <small class="text-secondary d-block mb-2 fw-bold"><i class="fas fa-building fa-xs me-1"></i>FIRMA</small>
    <span class="text-sm">{{ $client->company }}</span>
  </div>
  @endif

  {{-- Address --}}
  @if($client->address)
  <div class="mb-4">
    <small class="text-secondary d-block mb-2 fw-bold"><i class="fas fa-map-marker-alt fa-xs me-1"></i>ADRESA</small>
    <span class="text-sm">{{ $client->address }}</span>
  </div>
  @endif

  {{-- Notes --}}
  @if($client->notes)
  <div class="mb-4 p-3 bg-light rounded">
    <small class="text-secondary d-block mb-2 fw-bold"><i class="fas fa-sticky-note fa-xs me-1"></i>POZNÁMKY</small>
    <p class="text-sm mb-0" style="white-space: pre-wrap;">{{ $client->notes }}</p>
  </div>
  @endif

  {{-- Finance Stats --}}
  <div class="mb-4">
    <small class="text-secondary d-block mb-2 fw-bold"><i class="fas fa-chart-line fa-xs me-1"></i>FINANCE</small>
    <div class="row g-2 text-center">
      <div class="col-4">
        <small class="text-secondary d-block">Fakturováno</small>
        <span class="text-xs fw-bold text-info">{{ number_format($client->totalInvoiced(), 0, ',', ' ') }} Kč</span>
      </div>
      <div class="col-4">
        <small class="text-secondary d-block">Zaplaceno</small>
        <span class="text-xs fw-bold text-success">{{ number_format($client->totalPaid(), 0, ',', ' ') }} Kč</span>
      </div>
      <div class="col-4">
        <small class="text-secondary d-block">Dluh</small>
        <span class="text-xs fw-bold {{ $client->totalOutstanding() > 0 ? 'text-danger' : '' }}">{{ number_format($client->totalOutstanding(), 0, ',', ' ') }} Kč</span>
      </div>
    </div>
  </div>
</div>

<div class="modal-footer border-top bg-light pt-3">
  <div class="d-flex gap-2 w-100">
    @can('update', $client)
      <a href="{{ route('clients.edit', $client) }}" class="btn btn-primary btn-sm flex-grow-1">
        <i class="fas fa-edit me-1"></i>Upravit
      </a>
    @endcan
    <a href="{{ route('clients.show', $client) }}" class="btn btn-outline-primary btn-sm flex-grow-1" target="_blank">
      <i class="fas fa-external-link-alt me-1"></i>Otevřít
    </a>
  </div>
</div>
