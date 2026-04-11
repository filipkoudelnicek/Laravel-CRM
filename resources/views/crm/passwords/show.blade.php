@extends('layouts.user_type.auth')

@section('content')
<div class="row">
  <div class="col-lg-5">
    <div class="card mb-4">
      <div class="card-header pb-0 d-flex justify-content-between align-items-center">
        <h5 class="mb-0">{{ $password->title }}</h5>
        @can('update', $password)
          <a href="{{ route('passwords.edit', $password) }}" class="btn btn-outline-secondary btn-sm">Upravit</a>
        @endcan
      </div>
      <div class="card-body pt-2">
        <ul class="list-group list-group-flush">
          <li class="list-group-item ps-0 border-0">
            <small class="text-secondary">Typ</small><br>
            <span class="badge bg-gradient-light text-dark text-sm">{{ \App\Models\PasswordEntry::TYPES[$password->type] ?? $password->type }}</span>
          </li>

          @if($password->username)
          <li class="list-group-item ps-0 border-0">
            <small class="text-secondary">Uživ. jméno</small><br>
            <span class="font-weight-bold text-sm">{{ $password->username }}</span>
          </li>
          @endif

          <li class="list-group-item ps-0 border-0">
            <small class="text-secondary">Heslo</small><br>
            <div class="d-flex align-items-center gap-2 mt-1">
              <span id="pw-mask" class="font-weight-bold text-sm">••••••••</span>
              <span id="pw-revealed" class="font-weight-bold text-sm d-none font-monospace"></span>
              <button id="btn-reveal" class="btn bg-gradient-primary btn-sm py-1 px-2"
                      onclick="revealPassword({{ $password->id }})">
                <i class="fas fa-eye me-1"></i> Zobrazit
              </button>
              <button id="btn-copy" class="btn btn-outline-secondary btn-sm py-1 px-2 d-none"
                      onclick="copyPassword()">
                <i class="fas fa-copy me-1"></i> Kopírovat
              </button>
            </div>
          </li>

          @if($password->url)
          <li class="list-group-item ps-0 border-0">
            <small class="text-secondary">URL</small><br>
            <a href="{{ $password->url }}" target="_blank" rel="noopener" class="text-sm">{{ $password->url }}</a>
          </li>
          @endif

          {{-- Type-specific fields --}}
          @if($password->type === 'sftp')
            @if($password->sftp_host)
            <li class="list-group-item ps-0 border-0">
              <small class="text-secondary">SFTP Host</small><br>
              <span class="text-sm font-monospace">{{ $password->sftp_host }}:{{ $password->sftp_port ?? 22 }}</span>
            </li>
            @endif
            @if($password->sftp_path)
            <li class="list-group-item ps-0 border-0">
              <small class="text-secondary">Cesta</small><br>
              <span class="text-sm font-monospace">{{ $password->sftp_path }}</span>
            </li>
            @endif
          @elseif($password->type === 'hosting')
            @if($password->hosting_provider)
            <li class="list-group-item ps-0 border-0">
              <small class="text-secondary">Poskytovatel</small><br>
              <span class="text-sm">{{ $password->hosting_provider }}</span>
            </li>
            @endif
            @if($password->ftp_host)
            <li class="list-group-item ps-0 border-0">
              <small class="text-secondary">FTP Host</small><br>
              <span class="text-sm font-monospace">{{ $password->ftp_host }}</span>
            </li>
            @endif
          @endif

          @if($password->client)
          <li class="list-group-item ps-0 border-0">
            <small class="text-secondary">Klient</small><br>
            <a href="{{ route('clients.show', $password->client) }}" class="text-sm">{{ $password->client->name }}</a>
          </li>
          @endif

          @if($password->project)
          <li class="list-group-item ps-0 border-0">
            <small class="text-secondary">Projekt</small><br>
            <a href="{{ route('projects.show', $password->project) }}" class="text-sm">{{ $password->project->name }}</a>
          </li>
          @endif

          @if($password->notes)
          <li class="list-group-item ps-0 border-0">
            <small class="text-secondary">Poznámky</small><br>
            <p class="text-sm mb-0">{{ $password->notes }}</p>
          </li>
          @endif

          <li class="list-group-item ps-0 border-0">
            <small class="text-secondary">Vytvořil</small>
            <span class="text-sm ms-2">{{ $password->creator->name ?? '—' }}</span>
          </li>
        </ul>

        @can('delete', $password)
          <hr>
          <form method="POST" action="{{ route('passwords.destroy', $password) }}" onsubmit="return confirm('Smazat tento záznam?')">
            @csrf @method('DELETE')
            <button class="btn btn-danger btn-sm w-100">Smazat záznam</button>
          </form>
        @endcan
      </div>
    </div>
  </div>

  {{-- Audit log --}}
  <div class="col-lg-7">
    <div class="card mb-4">
      <div class="card-header pb-0">
        <h6 class="mb-0">Přístupový log</h6>
      </div>
      <div class="card-body px-0 pt-0 pb-2">
        <div class="table-responsive p-0">
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Uživatel</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Akce</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">IP</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Kdy</th>
              </tr>
            </thead>
            <tbody>
              @forelse($password->accessLogs as $log)
              <tr>
                <td><p class="text-xs font-weight-bold mb-0 ms-2">{{ $log->user->name ?? '—' }}</p></td>
                <td class="text-center">
                  @php $actionLabels = ['reveal'=>'Zobrazení','copy'=>'Kopírování']; @endphp
                  <span class="badge badge-sm bg-gradient-{{ $log->action === 'reveal' ? 'warning' : 'info' }}">
                    {{ $actionLabels[$log->action] ?? $log->action }}
                  </span>
                </td>
                <td class="text-center"><span class="text-xs">{{ $log->ip }}</span></td>
                <td class="text-center"><span class="text-xs">{{ $log->created_at->diffForHumans() }}</span></td>
              </tr>
              @empty
              <tr><td colspan="4" class="text-center py-3 text-sm text-secondary">Žádné záznamy přístupu.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
const revealUrl = '{{ route("passwords.reveal", $password) }}';
const csrfToken = '{{ csrf_token() }}';
let revealedPw = null;

async function revealPassword(id) {
  if (revealedPw) {
    toggleReveal(revealedPw);
    return;
  }
  const btn = document.getElementById('btn-reveal');
  btn.disabled = true;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Načítání…';
  try {
    const res = await fetch(revealUrl, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
    });
    if (!res.ok) throw new Error('Unauthorized');
    const data = await res.json();
    revealedPw = data.password;
    toggleReveal(revealedPw);
  } catch (e) {
    btn.disabled = false;
    btn.innerHTML = '<i class="fas fa-eye me-1"></i> Zobrazit';
    alert('Nelze zobrazit heslo: ' + e.message);
  }
}

function toggleReveal(pw) {
  const mask = document.getElementById('pw-mask');
  const revealed = document.getElementById('pw-revealed');
  const btn = document.getElementById('btn-reveal');
  const copyBtn = document.getElementById('btn-copy');

  if (revealed.classList.contains('d-none')) {
    revealed.textContent = pw;
    revealed.classList.remove('d-none');
    mask.classList.add('d-none');
    btn.innerHTML = '<i class="fas fa-eye-slash me-1"></i> Skrýt';
    btn.disabled = false;
    copyBtn.classList.remove('d-none');
  } else {
    revealed.classList.add('d-none');
    mask.classList.remove('d-none');
    btn.innerHTML = '<i class="fas fa-eye me-1"></i> Zobrazit';
    copyBtn.classList.add('d-none');
  }
}

function copyPassword() {
  if (!revealedPw) return;
  navigator.clipboard.writeText(revealedPw).then(() => {
    const btn = document.getElementById('btn-copy');
    const orig = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-check me-1"></i> Zkopírováno!';
    // Log copy action via same endpoint approach - fire and forget
    fetch('{{ route("passwords.reveal", $password) }}?action=copy', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
    });
    setTimeout(() => btn.innerHTML = orig, 2000);
  });
}
</script>
@endpush
@endsection

