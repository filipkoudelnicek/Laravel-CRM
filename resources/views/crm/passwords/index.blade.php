@extends('layouts.user_type.auth')

@section('content')
@php
  $typeIcons = [
    'general' => ['icon' => 'fa-key', 'color' => 'secondary'],
    'sftp' => ['icon' => 'fa-server', 'color' => 'info'],
    'admin' => ['icon' => 'fa-lock', 'color' => 'danger'],
    'hosting' => ['icon' => 'fa-cloud', 'color' => 'warning'],
  ];
@endphp

<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      <div class="card-header pb-3 pt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="mb-0">
            <i class="fas fa-vault me-2 text-primary opacity-75"></i>Trezor hesel
          </h5>
          <a href="{{ route('passwords.create') }}" class="btn bg-gradient-primary btn-sm">
            <i class="fas fa-plus me-1"></i> Nový záznam
          </a>
        </div>
        
        {{-- Search --}}
        <form method="GET" action="{{ route('passwords.index') }}" class="row g-2">
          <div class="col-auto flex-grow-1" style="max-width: 300px;">
            <input type="text" name="q" value="{{ request('q') }}" 
                   class="form-control form-control-sm" placeholder="Hledat…">
          </div>
          @if(request('q'))
            <div class="col-auto">
              <a href="{{ route('passwords.index') }}" class="btn btn-outline-secondary btn-sm">
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
                <th class="text-xs fw-bold text-secondary px-4 py-3">Typ</th>
                <th class="text-xs fw-bold text-secondary px-4 py-3">Uživatelské jméno</th>
                <th class="text-xs fw-bold text-secondary text-center px-4 py-3">Propojení</th>
                <th class="text-xs fw-bold text-secondary text-center px-4 py-3" style="width: 100px;">Akce</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($entries as $entry)
              <tr class="align-middle">
                <td class="px-4 py-3">
                  <a href="{{ route('passwords.show', $entry) }}" class="text-dark fw-500 text-decoration-none">
                    {{ $entry->title }}
                  </a>
                  @if($entry->url)
                    <br><small class="text-secondary">
                      <i class="fas fa-link fa-xs opacity-75"></i>
                      <a href="{{ $entry->url }}" target="_blank" rel="noopener" class="text-decoration-none text-secondary">
                        {{ parse_url($entry->url, PHP_URL_HOST) }}
                      </a>
                    </small>
                  @endif
                </td>
                <td class="px-4 py-3">
                  <span class="badge bg-gradient-{{ $typeIcons[$entry->type]['color'] ?? 'secondary' }} px-2">
                    <i class="fas {{ $typeIcons[$entry->type]['icon'] ?? 'fa-key' }} fa-xs me-1 opacity-75"></i>
                    {{ ucfirst($entry->type) }}
                  </span>
                </td>
                <td class="px-4 py-3">
                  <small class="text-secondary">{{ $entry->username ?? '—' }}</small>
                </td>
                <td class="px-4 py-3 text-center">
                  <small>
                    @if($entry->client)
                      <span class="badge bg-light text-dark px-2">
                        <i class="fas fa-user fa-xs me-1 opacity-75"></i>
                        {{ $entry->client->name }}
                      </span>
                    @endif
                    @if($entry->project)
                      <span class="badge bg-light text-dark px-2">
                        <i class="fas fa-folder fa-xs me-1 opacity-75"></i>
                        {{ $entry->project->name }}
                      </span>
                    @endif
                    @if(!$entry->client && !$entry->project)
                      <span class="text-secondary">—</span>
                    @endif
                  </small>
                </td>
                <td class="px-4 py-3 text-center">
                  <div class="btn-group btn-group-sm" role="group">
                    <a href="{{ route('passwords.show', $entry) }}" class="btn btn-outline-secondary btn-sm" title="Zobrazit">
                      <i class="fas fa-eye fa-sm"></i>
                    </a>
                    @can('update', $entry)
                      <a href="{{ route('passwords.edit', $entry) }}" class="btn btn-outline-secondary btn-sm" title="Upravit">
                        <i class="fas fa-edit fa-sm"></i>
                      </a>
                    @endcan
                    @can('delete', $entry)
                      <form method="POST" action="{{ route('passwords.destroy', $entry) }}" class="d-inline"
                            onsubmit="return confirm('Smazat tento záznam?')">
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
                <td colspan="5" class="px-4 py-4">
                  <div class="text-center text-secondary">
                    <i class="fas fa-inbox fa-3x opacity-25 d-block mb-2"></i>
                    <small>Žádné záznamy nenalezeny.</small>
                  </div>
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        
        @if($entries->hasPages())
          <div class="card-footer bg-white px-4 py-3 border-top">
            {{ $entries->links() }}
          </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection
    </div>
  </div>
</div>
@endsection

