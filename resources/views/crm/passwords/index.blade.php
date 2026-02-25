@extends('layouts.user_type.auth')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card mb-4 mx-4">
      <div class="card-header pb-0">
        <div class="d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Trezor hesel</h5>
          <a href="{{ route('passwords.create') }}" class="btn bg-gradient-primary btn-sm">+ Nový záznam</a>
        </div>
        <form method="GET" action="{{ route('passwords.index') }}" class="mt-3 mb-0">
          <div class="input-group input-group-sm" style="max-width:300px">
            <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Hledat…">
            <button class="btn btn-outline-secondary mb-0" type="submit">Hledat</button>
            @if(request('q'))<a href="{{ route('passwords.index') }}" class="btn btn-outline-secondary">×</a>@endif
          </div>
        </form>
      </div>
      <div class="card-body px-0 pt-0 pb-2">
        <div class="table-responsive p-0">
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Název</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Uživ. jméno</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Propojení</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Heslo</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Akce</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($entries as $entry)
              <tr>
                <td>
                  <div class="d-flex px-2 py-1">
                    <div class="d-flex flex-column justify-content-center">
                      <h6 class="mb-0 text-sm">
                        <a href="{{ route('passwords.show', $entry) }}" class="text-dark">{{ $entry->title }}</a>
                      </h6>
                      @if($entry->url)
                        <a href="{{ $entry->url }}" target="_blank" rel="noopener" class="text-xs text-secondary">{{ parse_url($entry->url, PHP_URL_HOST) }}</a>
                      @endif
                    </div>
                  </div>
                </td>
                <td><p class="text-xs font-weight-bold mb-0">{{ $entry->username ?? '—' }}</p></td>
                <td class="text-center">
                  <p class="text-xs mb-0">
                    @if($entry->client) <span class="badge bg-gradient-info me-1">{{ $entry->client->name }}</span> @endif
                    @if($entry->project) <span class="badge bg-gradient-secondary">{{ $entry->project->name }}</span> @endif
                    @if(!$entry->client && !$entry->project) — @endif
                  </p>
                </td>
                <td class="text-center">
                  {{-- Never show password in list --}}
                  <span class="text-sm font-weight-bold tracking-wide">••••••••</span>
                </td>
                <td class="text-center">
                  <a href="{{ route('passwords.show', $entry) }}" class="text-secondary font-weight-bold text-xs me-2">Zobrazit</a>
                  @can('update', $entry)
                    <a href="{{ route('passwords.edit', $entry) }}" class="text-secondary font-weight-bold text-xs me-2">Upravit</a>
                  @endcan
                  @can('delete', $entry)
                    <form method="POST" action="{{ route('passwords.destroy', $entry) }}" class="d-inline"
                          onsubmit="return confirm('Smazat tento záznam?')">
                      @csrf @method('DELETE')
                      <button class="btn btn-link text-danger font-weight-bold text-xs p-0 mb-0">Smazat</button>
                    </form>
                  @endcan
                </td>
              </tr>
              @empty
              <tr><td colspan="5" class="text-center py-3 text-sm text-secondary">Žádné záznamy nenalezeny.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <div class="px-4 pt-3">{{ $entries->links() }}</div>
      </div>
    </div>
  </div>
</div>
@endsection

