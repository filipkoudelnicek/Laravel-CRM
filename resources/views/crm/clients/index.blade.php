@extends('layouts.user_type.auth')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      <div class="card-header pb-3 pt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="mb-0">
            <i class="fas fa-users me-2 text-primary opacity-75"></i>Klienti
          </h5>
          @can('create', \App\Models\Client::class)
            <a href="{{ route('clients.create') }}" class="btn bg-gradient-primary btn-sm">
              <i class="fas fa-plus me-1"></i> Nový klient
            </a>
          @endcan
        </div>
        
        {{-- Search --}}
        <form method="GET" action="{{ route('clients.index') }}" class="row g-2">
          <div class="col-auto flex-grow-1" style="max-width: 400px;">
            <input type="text" name="q" value="{{ request('q') }}" 
                   class="form-control form-control-sm" placeholder="Hledat jméno / firmu…">
          </div>
          @if(request('q'))
            <div class="col-auto">
              <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary btn-sm">
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
                <th class="text-xs fw-bold text-secondary px-4 py-3">Jméno</th>
                <th class="text-xs fw-bold text-secondary px-4 py-3">Firma</th>
                <th class="text-xs fw-bold text-secondary px-4 py-3">E-mail</th>
                <th class="text-xs fw-bold text-secondary text-center px-4 py-3">Telefon</th>
                <th class="text-xs fw-bold text-secondary text-center px-4 py-3">Projekty</th>
                <th class="text-xs fw-bold text-secondary text-center px-4 py-3" style="width: 100px;">Akce</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($clients as $client)
              <tr class="align-middle">
                <td class="px-4 py-3">
                  <button onclick="openDetailModal('{{ route('clients.modal', $client) }}')" 
                          class="btn btn-link text-dark p-0 text-decoration-none text-start fw-500"
                          style="font-weight: 500;">
                    {{ $client->name }}
                  </button>
                </td>
                <td class="px-4 py-3">
                  <small class="text-secondary">{{ $client->company ?? '—' }}</small>
                </td>
                <td class="px-4 py-3">
                  @if($client->email)
                    <a href="mailto:{{ $client->email }}" class="text-secondary text-decoration-none">
                      <i class="fas fa-envelope fa-xs me-1 opacity-75"></i>{{ $client->email }}
                    </a>
                  @else
                    <span class="text-secondary">—</span>
                  @endif
                </td>
                <td class="px-4 py-3 text-center">
                  @if($client->phone)
                    <a href="tel:{{ $client->phone }}" class="text-secondary text-decoration-none">
                      <i class="fas fa-phone fa-xs me-1 opacity-75"></i>{{ $client->phone }}
                    </a>
                  @else
                    <span class="text-secondary">—</span>
                  @endif
                </td>
                <td class="px-4 py-3 text-center">
                  <span class="badge bg-gradient-secondary px-2">
                    <i class="fas fa-folder fa-xs me-1 opacity-75"></i>
                    {{ $client->projects_count }}
                  </span>
                </td>
                <td class="px-4 py-3 text-center">
                  <div class="btn-group btn-group-sm" role="group">
                    <a href="{{ route('clients.show', $client) }}" class="btn btn-outline-secondary btn-sm" title="Otevřít">
                      <i class="fas fa-external-link-alt fa-sm"></i>
                    </a>
                    @can('update', $client)
                      <a href="{{ route('clients.edit', $client) }}" class="btn btn-outline-secondary btn-sm" title="Upravit">
                        <i class="fas fa-edit fa-sm"></i>
                      </a>
                    @endcan
                    @can('delete', $client)
                      <form method="POST" action="{{ route('clients.destroy', $client) }}" class="d-inline"
                            onsubmit="return confirm('Smazat tohoto klienta?')">
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
                    <small>Žádní klienti nenalezeni.</small>
                  </div>
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        
        @if($clients->hasPages())
          <div class="card-footer bg-white px-4 py-3 border-top">
            {{ $clients->links() }}
          </div>
        @endif
      </div>
    </div>
  </div>
</div>

@include('components.detail-modal')
@endsection

