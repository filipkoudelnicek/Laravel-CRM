<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3" id="sidenav-main">
  <div class="sidenav-header">
    <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
    <a class="align-items-center d-flex m-0 navbar-brand text-wrap" href="{{ route('dashboard') }}">
        <img src="{{ asset('assets/img/logo-ct.png') }}" class="navbar-brand-img h-100" alt="CRM">
        <span class="ms-3 font-weight-bold">CRM Codencio</span>
    </a>
  </div>
  <hr class="horizontal dark mt-0">
  <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
    <ul class="navbar-nav">

      {{-- Dashboard --}}
      <li class="nav-item">
        <a class="nav-link {{ Request::is('dashboard') ? 'active' : '' }}" href="{{ url('dashboard') }}">
          <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="fas fa-tachometer-alt text-dark text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Nástěnka</span>
        </a>
      </li>

      {{-- ── CRM ── --}}
      <li class="nav-item mt-2">
        <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">CRM</h6>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ Request::is('clients*') ? 'active' : '' }}" href="{{ route('clients.index') }}">
          <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="fas fa-building text-dark text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Klienti</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ Request::is('projects*') ? 'active' : '' }}" href="{{ route('projects.index') }}">
          <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="fas fa-project-diagram text-dark text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Projekty</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ Request::is('tasks*') ? 'active' : '' }}" href="{{ route('tasks.index') }}">
          <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="fas fa-tasks text-dark text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Úkoly</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ Request::is('passwords*') ? 'active' : '' }}" href="{{ route('passwords.index') }}">
          <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="fas fa-key text-dark text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Hesla</span>
        </a>
      </li>

      {{-- ── Finance ── --}}
      <li class="nav-item mt-2">
        <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Finance</h6>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ Request::is('invoices*') ? 'active' : '' }}" href="{{ route('invoices.index') }}">
          <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="fas fa-file-invoice-dollar text-dark text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Faktury</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ Request::is('support-plans*') ? 'active' : '' }}" href="{{ route('support-plans.index') }}">
          <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="fas fa-life-ring text-dark text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Podpůrné plány</span>
        </a>
      </li>

      {{-- ── System ── --}}
      <li class="nav-item mt-2">
        <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Systém</h6>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ Request::is('notifications*') ? 'active' : '' }}" href="{{ route('notifications.index') }}">
          <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="fas fa-bell text-dark text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Notifikace</span>
        </a>
      </li>
      @if(auth()->user()?->role === 'admin')
      <li class="nav-item">
        <a class="nav-link {{ Request::is('user-management') ? 'active' : '' }}" href="{{ url('user-management') }}">
          <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="fas fa-users-cog text-dark text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Správa uživatelů</span>
        </a>
      </li>
      @endif

    </ul>
  </div>
</aside>

