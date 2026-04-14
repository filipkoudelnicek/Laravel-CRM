<!-- Navbar -->
<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
    <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
            <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('dashboard') }}">CRM</a></li>
            <li class="breadcrumb-item text-sm text-dark active text-capitalize" aria-current="page">{{ str_replace('-', ' ', Request::path()) }}</li>
            </ol>
            <h6 class="font-weight-bolder mb-0 text-capitalize">{{ str_replace('-', ' ', Request::path()) }}</h6>
        </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4 d-flex justify-content-end" id="navbar">
            <ul class="navbar-nav justify-content-end align-items-center">

            {{-- ── Dark Mode Toggle ── --}}
            <li class="nav-item d-flex align-items-center pe-3">
                <a href="javascript:;" class="nav-link text-body p-0 dark-mode-toggle" id="darkModeToggle" title="Přepnout tmavý/světlý režim">
                  <i class="fas {{ auth()->user()->dark_mode ? 'fa-sun' : 'fa-moon' }} fs-5"></i>
                </a>
            </li>

            {{-- ── Notification Bell ── --}}
            @php
              $unreadNotifications = auth()->user()->unreadNotifications()->latest()->limit(8)->get();
              $unreadCount = auth()->user()->unreadNotifications()->count();
            @endphp
            <li class="nav-item dropdown pe-2 d-flex align-items-center">
                <a href="javascript:;" class="nav-link text-body p-0 position-relative" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                  <i class="fa fa-bell cursor-pointer fs-5"></i>
                  @if($unreadCount > 0)
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:.55rem;padding:2px 5px;">
                      {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                    </span>
                  @endif
                </a>
                <ul class="dropdown-menu dropdown-menu-end px-2 py-3 me-sm-n4" aria-labelledby="notificationDropdown" style="min-width:340px;max-height:420px;overflow-y:auto;">
                  <li class="px-3 pb-2">
                    <h6 class="text-sm mb-0">Oznámení</h6>
                  </li>
                  <li><hr class="dropdown-divider mt-0"></li>
                  @forelse($unreadNotifications as $notif)
                  @php
                    $notifData = $notif->data;
                    $notifUrl = route('notifications.index');
                    if (!empty($notifData['task_id'])) {
                        $notifUrl = url('tasks/' . $notifData['task_id']);
                    } elseif (!empty($notifData['project_id'])) {
                        $notifUrl = url('projects/' . $notifData['project_id']);
                    }
                    $notifIcon = 'fa-bell';
                    $type = class_basename($notif->type);
                    if (str_contains($type, 'Task')) $notifIcon = 'fa-tasks';
                    elseif (str_contains($type, 'Project')) $notifIcon = 'fa-project-diagram';
                    elseif (str_contains($type, 'Comment') || str_contains($type, 'Mention')) $notifIcon = 'fa-comment';
                    elseif (str_contains($type, 'Status')) $notifIcon = 'fa-exchange-alt';
                  @endphp
                  <li class="mb-1">
                      <a class="dropdown-item border-radius-md py-2 mark-notification-read notification-unread"
                       href="{{ $notifUrl }}"
                        data-id="{{ $notif->id }}">
                      <div class="d-flex align-items-center">
                        <div class="icon icon-shape icon-sm bg-gradient-info shadow text-center border-radius-md me-3 d-flex align-items-center justify-content-center">
                          <i class="fas {{ $notifIcon }} text-white text-xs"></i>
                        </div>
                        <div class="flex-grow-1">
                          <h6 class="text-sm font-weight-normal mb-0">
                            {{ Str::limit($notifData['message'] ?? 'Nová notifikace', 65) }}
                          </h6>
                          <p class="text-xs text-secondary mb-0">
                            <i class="fa fa-clock me-1"></i>{{ $notif->created_at->diffForHumans() }}
                          </p>
                        </div>
                      </div>
                    </a>
                  </li>
                  @empty
                  <li class="px-3 py-2 text-center">
                    <span class="text-sm text-secondary">Žádná nová oznámení</span>
                  </li>
                  @endforelse
                  @if($unreadCount > 0)
                  <li><hr class="dropdown-divider"></li>
                  <li class="d-flex justify-content-between align-items-center px-3">
                    <a href="{{ route('notifications.index') }}" class="text-xs font-weight-bold text-primary">Zobrazit vše</a>
                    <form method="POST" action="{{ route('notifications.readAll') }}" class="d-inline">
                      @csrf
                      <button class="btn btn-link text-xs p-0 m-0 text-secondary">Označit vše jako přečtené</button>
                    </form>
                  </li>
                  @endif
                </ul>
            </li>

            {{-- ── User Avatar Dropdown ── --}}
            <li class="nav-item dropdown d-flex align-items-center ps-3">
                <a href="javascript:;" class="nav-link text-body p-0 d-flex align-items-center" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                  <div class="avatar avatar-sm rounded-circle bg-gradient-dark d-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                    <span class="text-white text-xs font-weight-bold">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</span>
                  </div>
                  <span class="d-sm-inline d-none text-sm font-weight-bold ms-2">{{ auth()->user()->name }}</span>
                  <i class="fas fa-chevron-down text-xs ms-1 d-sm-inline d-none"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end py-2" aria-labelledby="userDropdown" style="min-width:180px;">
                  <li class="px-3 pb-2 border-bottom">
                    <p class="text-xs text-secondary mb-0">Přihlášen jako</p>
                    <p class="text-sm font-weight-bold mb-0">{{ auth()->user()->email }}</p>
                  </li>
                  <li>
                    <a class="dropdown-item py-2" href="{{ url('user-profile') }}">
                      <i class="fas fa-user-circle text-sm me-2 text-secondary"></i>Profil
                    </a>
                  </li>
                  <li><hr class="dropdown-divider my-1"></li>
                  <li>
                    <a class="dropdown-item py-2 text-danger" href="{{ url('/logout') }}">
                      <i class="fas fa-sign-out-alt text-sm me-2"></i>Odhlásit se
                    </a>
                  </li>
                </ul>
            </li>

            {{-- Mobile sidebar toggle --}}
            <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
                <a href="javascript:;" class="nav-link text-body p-0" id="iconNavbarSidenav">
                <div class="sidenav-toggler-inner">
                    <i class="sidenav-toggler-line"></i>
                    <i class="sidenav-toggler-line"></i>
                    <i class="sidenav-toggler-line"></i>
                </div>
                </a>
            </li>
            </ul>
        </div>
    </div>
</nav>
<!-- End Navbar -->

@push('scripts')
<script>
document.querySelectorAll('.mark-notification-read').forEach(function(el) {
  el.addEventListener('click', function(e) {
    e.preventDefault();
    var url = this.href;
    var id = this.dataset.id;
    fetch('{{ route("notifications.index") }}/' + id + '/read', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Accept': 'application/json'
      }
    }).finally(function() {
      window.location.href = url;
    });
  });
});

// Dark mode toggle
(function() {
  var toggle = document.getElementById('darkModeToggle');
  if (!toggle) return;

  var pending = false;

  function applyDarkModeState(isDark) {
    document.body.classList.toggle('dark-mode', isDark);
    var icon = toggle.querySelector('i');
    if (icon) {
      icon.classList.toggle('fa-moon', !isDark);
      icon.classList.toggle('fa-sun', isDark);
    }
  }

  toggle.addEventListener('click', function(e) {
    e.preventDefault();
    if (pending) return;

    var previousState = document.body.classList.contains('dark-mode');
    var nextState = !previousState;

    pending = true;
    toggle.classList.add('disabled');
    toggle.setAttribute('aria-disabled', 'true');

    // Optimistic UI update for immediate feedback.
    applyDarkModeState(nextState);

    fetch('{{ route("toggle-dark-mode") }}', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      },
      credentials: 'same-origin',
      body: JSON.stringify({ dark_mode: nextState })
    })
      .then(function(response) {
        if (!response.ok) {
          throw new Error('Failed to save dark mode preference');
        }
        return response.json();
      })
      .then(function(data) {
        applyDarkModeState(!!data.dark_mode);
      })
      .catch(function() {
        applyDarkModeState(previousState);
      })
      .finally(function() {
        pending = false;
        toggle.classList.remove('disabled');
        toggle.removeAttribute('aria-disabled');
      });
  });
})();
</script>
@endpush

