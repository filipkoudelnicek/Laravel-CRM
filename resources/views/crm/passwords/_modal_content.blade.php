@php
  $typeIcons = [
    'general' => ['icon' => 'fa-key', 'color' => 'secondary', 'label' => 'Obecné'],
    'sftp' => ['icon' => 'fa-server', 'color' => 'info', 'label' => 'SFTP'],
    'admin' => ['icon' => 'fa-lock', 'color' => 'danger', 'label' => 'Admin'],
    'hosting' => ['icon' => 'fa-cloud', 'color' => 'warning', 'label' => 'Hosting'],
  ];
@endphp

<div class="modal-header border-0 bg-transparent pb-2">
  <h5 class="modal-title fs-6 fw-bold">{{ $password->title }}</h5>
  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body pb-0">
  {{-- Type Badge --}}
  <div class="mb-4">
    <small class="text-secondary d-block mb-2 fw-bold"><i class="fas {{ $typeIcons[$password->type]['icon'] }} fa-xs me-1"></i>TYP</small>
    <span class="badge bg-gradient-{{ $typeIcons[$password->type]['color'] }} px-3 py-2">
      {{ $typeIcons[$password->type]['label'] }}
    </span>
  </div>

  {{-- Username --}}
  @if($password->username)
    <div class="mb-4">
      <small class="text-secondary d-block mb-2 fw-bold">UŽIVATELSKÉ JMÉNO</small>
      <span class="text-sm font-monospace text-dark">{{ $password->username }}</span>
    </div>
  @endif

  {{-- URL --}}
  @if($password->url)
    <div class="mb-4">
      <small class="text-secondary d-block mb-2 fw-bold">URL</small>
      <a href="{{ $password->url }}" target="_blank" rel="noopener" class="text-decoration-none text-dark fw-500">
        {{ parse_url($password->url, PHP_URL_HOST) }} <i class="fas fa-external-link-alt fa-xs ms-1 opacity-50"></i>
      </a>
    </div>
  @endif

  {{-- Type-specific fields --}}
  @if($password->type === 'sftp')
    @if($password->sftp_host)
      <div class="mb-4">
        <small class="text-secondary d-block mb-2 fw-bold">SFTP HOST</small>
        <span class="text-sm font-monospace text-dark">{{ $password->sftp_host }}:{{ $password->sftp_port ?? 22 }}</span>
      </div>
    @endif
    @if($password->sftp_path)
      <div class="mb-4">
        <small class="text-secondary d-block mb-2 fw-bold">CESTA</small>
        <span class="text-sm font-monospace text-dark">{{ $password->sftp_path }}</span>
      </div>
    @endif
  @elseif($password->type === 'hosting')
    @if($password->hosting_provider)
      <div class="mb-4">
        <small class="text-secondary d-block mb-2 fw-bold">POSKYTOVATEL</small>
        <span class="text-sm text-dark">{{ $password->hosting_provider }}</span>
      </div>
    @endif
    @if($password->ftp_host)
      <div class="mb-4">
        <small class="text-secondary d-block mb-2 fw-bold">FTP HOST</small>
        <span class="text-sm font-monospace text-dark">{{ $password->ftp_host }}</span>
      </div>
    @endif
  @endif

  {{-- Client & Project Links --}}
  @if($password->client)
    <div class="mb-4">
      <small class="text-secondary d-block mb-2 fw-bold"><i class="fas fa-user fa-xs me-1"></i>KLIENT</small>
      <a href="{{ route('clients.show', $password->client) }}" class="text-decoration-none text-dark fw-500">
        {{ $password->client->name }} <i class="fas fa-external-link-alt fa-xs ms-1 opacity-50"></i>
      </a>
    </div>
  @endif

  @if($password->project)
    <div class="mb-4">
      <small class="text-secondary d-block mb-2 fw-bold"><i class="fas fa-folder fa-xs me-1"></i>PROJEKT</small>
      <a href="{{ route('projects.show', $password->project) }}" class="text-decoration-none text-dark fw-500">
        {{ $password->project->name }} <i class="fas fa-external-link-alt fa-xs ms-1 opacity-50"></i>
      </a>
    </div>
  @endif

  {{-- Notes --}}
  @if($password->notes)
    <div class="mb-4 p-3 bg-light rounded">
      <small class="text-secondary d-block mb-2 fw-bold">POZNÁMKY</small>
      <p class="text-sm mb-0" style="white-space: pre-wrap;">{{ $password->notes }}</p>
    </div>
  @endif
</div>

<div class="modal-footer bg-light">
  <a href="{{ route('passwords.edit', $password) }}" class="btn bg-gradient-primary btn-sm">Upravit</a>
  <a href="{{ route('passwords.show', $password) }}" class="btn btn-outline-secondary btn-sm">Otevřít</a>
</div>
