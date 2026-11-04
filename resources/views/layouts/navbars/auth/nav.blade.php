@php
  $sectionLabels = ['dashboard'=>'Moje práce','projects'=>'Projekty','tasks'=>'Úkoly','clients'=>'Klienti','invoices'=>'Faktury','support-plans'=>'Služby a období','passwords'=>'Přístupy','notifications'=>'Oznámení','user-management'=>'Lidé a přístupy','users'=>'Lidé a přístupy','user-profile'=>'Můj profil'];
  $sectionLabel = $sectionLabels[request()->segment(1)] ?? 'Pracovní prostor';
  $notificationCount = auth()->user()->unreadNotifications()->count();
@endphp
<header class="workspace-topbar">
  <div class="d-flex align-items-center gap-3"><button type="button" class="workspace-menu-button" aria-label="Otevřít navigaci" aria-controls="workspace-sidebar" aria-expanded="false">☰</button><span class="topbar-context">Pracovní prostor <span aria-hidden="true">/</span> <strong>{{ $sectionLabel }}</strong></span></div>
  <div class="d-flex gap-3 align-items-center"><button type="button" class="theme-toggle" aria-label="{{ auth()->user()->dark_mode ? 'Zapnout světlý režim' : 'Zapnout tmavý režim' }}" aria-pressed="{{ auth()->user()->dark_mode ? 'true' : 'false' }}" id="darkModeToggle" data-url="{{ route('toggle-dark-mode') }}">{{ auth()->user()->dark_mode ? 'Světlý režim' : 'Tmavý režim' }}</button><a href="{{ route('notifications.index') }}" class="notification-link" aria-label="Oznámení: {{ $notificationCount }} nepřečtených"><i class="fa-solid fa-bell" aria-hidden="true"></i>@if($notificationCount)<span>{{ $notificationCount }}</span>@endif</a><a href="{{ url('logout') }}" class="text-sm text-secondary">Odhlásit</a></div>
</header>
