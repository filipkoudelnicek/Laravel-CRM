@props(['client'])

<div class="modal-header border-bottom bg-transparent">
  <h5 class="modal-title">{{ $client->name }}</h5>
</div>

<div class="modal-body">
  @if($client->company)
  <div class="mb-3">
    <small class="text-secondary d-block mb-1">
      <i class="fas fa-building fa-xs me-1 opacity-75"></i>Firma
    </small>
    <span class="text-sm fw-500">{{ $client->company }}</span>
  </div>
  @endif

  @if($client->email)
  <div class="mb-3">
    <small class="text-secondary d-block mb-1">
      <i class="fas fa-envelope fa-xs me-1 opacity-75"></i>E-mail
    </small>
    <a href="mailto:{{ $client->email }}" class="text-sm text-decoration-none">{{ $client->email }}</a>
  </div>
  @endif

  @if($client->phone)
  <div class="mb-3">
    <small class="text-secondary d-block mb-1">
      <i class="fas fa-phone fa-xs me-1 opacity-75"></i>Telefon
    </small>
    <a href="tel:{{ $client->phone }}" class="text-sm text-decoration-none">{{ $client->phone }}</a>
  </div>
  @endif

  @if($client->address)
  <div class="mb-3">
    <small class="text-secondary d-block mb-1">
      <i class="fas fa-map-marker-alt fa-xs me-1 opacity-75"></i>Adresa
    </small>
    <span class="text-sm">{{ $client->address }}</span>
  </div>
  @endif

  @if($client->city || $client->country)
  <div class="mb-3">
    <small class="text-secondary d-block mb-1">
      <i class="fas fa-globe fa-xs me-1 opacity-75"></i>Lokace
    </small>
    <span class="text-sm">
      @if($client->city) {{ $client->city }} @endif
      @if($client->country) {{ $client->country }} @endif
    </span>
  </div>
  @endif

  @if($client->notes)
  <div class="mb-3">
    <small class="text-secondary d-block mb-1">
      <i class="fas fa-sticky-note fa-xs me-1 opacity-75"></i>Poznámky
    </small>
    <p class="text-sm mb-0">{{ $client->notes }}</p>
  </div>
  @endif

  <div class="row my-3 text-center">
    <div class="col-6">
      <small class="text-secondary d-block">Projektů</small>
      <span class="badge bg-light text-dark">
        <i class="fas fa-folder fa-xs me-1 opacity-75"></i>
        {{ $client->projects_count }}
      </span>
    </div>
    <div class="col-6">
      <small class="text-secondary d-block">Fakturáno</small>
      <span class="badge bg-light text-dark">{{ number_format($client->totalInvoiced(), 0, ',', ' ') }} Kč</span>
    </div>
  </div>

  <hr class="my-3">

  <div class="btn-group btn-group-sm w-100" role="group">
    @can('update', $client)
      <a href="{{ route('clients.edit', $client) }}" class="btn btn-outline-secondary" title="Upravit">
        <i class="fas fa-edit me-1"></i>Upravit
      </a>
    @endcan
    <a href="{{ route('clients.show', $client) }}" class="btn btn-outline-primary" title="Otevřít v novém okně">
      <i class="fas fa-external-link-alt me-1"></i>Otevřít
    </a>
  </div>
</div>
</div>
