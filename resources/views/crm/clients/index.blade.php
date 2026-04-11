@extends('layouts.user_type.auth')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card mb-4 mx-4">
      <div class="card-header pb-0">
        <div class="d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Klienti</h5>
          @can('create', \App\Models\Client::class)
            <a href="{{ route('clients.create') }}" class="btn bg-gradient-primary btn-sm">+ Nový klient</a>
          @endcan
        </div>
        {{-- Search --}}
        <form method="GET" action="{{ route('clients.index') }}" class="mt-3 mb-0">
          <div class="input-group input-group-sm" style="max-width:340px">
            <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Hledat jméno / firmu…">
            <button class="btn btn-outline-secondary mb-0" type="submit">Hledat</button>
            @if(request('q'))<a href="{{ route('clients.index') }}" class="btn btn-outline-secondary">×</a>@endif
          </div>
        </form>
      </div>
      <div class="card-body px-0 pt-0 pb-2">
        <div class="table-responsive p-0">
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Jméno</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Firma</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">E-mail</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Projekty</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Akce</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($clients as $client)
              <tr>
                <td>
                  <div class="d-flex px-2 py-1">
                    <div class="d-flex flex-column justify-content-center">
                      <h6 class="mb-0 text-sm">
                        <button onclick="openDetailModal('{{ route('clients.modal', $client) }}')" 
                                class="btn btn-link text-dark p-0 text-decoration-none text-start"
                                style="font-weight: 500;">
                          {{ $client->name }}
                        </button>
                      </h6>
                      @if($client->phone)<p class="text-xs text-secondary mb-0">{{ $client->phone }}</p>@endif
                    </div>
                  </div>
                </td>
                <td><p class="text-xs font-weight-bold mb-0">{{ $client->company ?? '—' }}</p></td>
                <td class="text-center"><p class="text-xs font-weight-bold mb-0">{{ $client->email ?? '—' }}</p></td>
                <td class="text-center">
                  <span class="badge badge-sm bg-gradient-secondary">{{ $client->projects_count }}</span>
                </td>
                <td class="text-center">
                  <div class="btn-group btn-group-sm" role="group">
                    <a href="{{ route('clients.show', $client) }}" class="btn btn-outline-secondary" title="Otevřít">
                      <i class="fas fa-external-link-alt fa-xs"></i>
                    </a>
                    @can('update', $client)
                      <a href="{{ route('clients.edit', $client) }}" class="btn btn-outline-secondary" title="Upravit">
                        <i class="fas fa-edit fa-xs"></i>
                      </a>
                    @endcan
                    @can('delete', $client)
                      <form method="POST" action="{{ route('clients.destroy', $client) }}" class="d-inline"
                            onsubmit="return confirm('Smazat tohoto klienta?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-outline-danger" title="Smazat" type="submit">
                          <i class="fas fa-trash fa-xs"></i>
                        </button>
                      </form>
                    @endcan
                  </div>
                </td>
              </tr>
              @empty
              <tr><td colspan="5" class="text-center py-3 text-sm text-secondary">Žádní klienti nenalezeni.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <div class="px-4 pt-3">
          {{ $clients->links() }}
        </div>
      </div>
    </div>
  </div>
</div>

@include('components.detail-modal')
@endsection

