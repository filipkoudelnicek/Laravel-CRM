@props(['client'])

<div class="modal-header border-bottom">
  <h5 class="modal-title">{{ $client->name }}</h5>
  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
  <div class="row mb-3">
    <div class="col-6">
      <small class="text-secondary d-block">E-mail</small>
      @if($client->email)
        <a href="mailto:{{ $client->email }}" class="text-sm text-dark">{{ $client->email }}</a>
      @else
        <span class="text-sm text-secondary">—</span>
      @endif
    </div>
    <div class="col-6 text-end">
      <small class="text-secondary d-block">Telefon</small>
      @if($client->phone)
        <a href="tel:{{ $client->phone }}" class="text-sm text-dark">{{ $client->phone }}</a>
      @else
        <span class="text-sm text-secondary">—</span>
      @endif
    </div>
  </div>

  @if($client->company)
    <hr>
    <small class="text-secondary">Firma</small>
    <p class="text-sm font-weight-bold mb-0">{{ $client->company }}</p>
  @endif

  @if($client->city || $client->country)
    <hr>
    <small class="text-secondary">Lokace</small>
    <p class="text-sm mb-0">
      @if($client->city) {{ $client->city }} @endif
      @if($client->country) {{ $client->country }} @endif
    </p>
  @endif

  @if($client->projects_count > 0)
    <hr>
    <small class="text-secondary d-block">Projekty</small>
    <span class="badge bg-gradient-secondary">{{ $client->projects_count }}</span>
  @endif
</div>

<div class="modal-footer border-top">
  <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Zavřít</button>
  @can('update', $client)
    <a href="{{ route('clients.edit', $client) }}" class="btn bg-gradient-primary btn-sm">
      <i class="fas fa-edit me-1"></i> Upravit
    </a>
  @endcan
  <a href="{{ route('clients.show', $client) }}" class="btn btn-outline-primary btn-sm">
    <i class="fas fa-expand me-1"></i> Otevřít na plné stránce
  </a>
</div>
