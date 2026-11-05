@php
  $sectionLabels = ['dashboard'=>'Moje práce','projects'=>'Projekty','tasks'=>'Úkoly','clients'=>'Klienti','invoices'=>'Faktury','support-plans'=>'Služby a období','passwords'=>'Přístupy','notifications'=>'Oznámení','user-management'=>'Lidé a přístupy','users'=>'Lidé a přístupy','user-profile'=>'Můj profil'];
  $sectionLabel = $sectionLabels[request()->segment(1)] ?? 'Pracovní prostor';
  $sectionIcon = ['dashboard'=>'home','projects'=>'folder','tasks'=>'tasks','clients'=>'clients','support-plans'=>'calendar','invoices'=>'invoice','passwords'=>'key','notifications'=>'bell','user-management'=>'people','users'=>'people'][request()->segment(1)] ?? 'home';
  $notificationCount = auth()->user()->unreadNotifications()->count();
@endphp
<header class="workspace-topbar">
  <div class="d-flex align-items-center gap-2" style="min-width:0">
    <button type="button" class="workspace-menu-button" aria-label="Otevřít navigaci" aria-controls="workspace-sidebar" aria-expanded="false"><x-icon name="menu" /></button>
    <span class="topbar-context"><x-icon :name="$sectionIcon" /><strong>{{ $sectionLabel }}</strong></span>
  </div>
  <div class="topbar-actions">
    <button type="button" class="theme-toggle" id="darkModeToggle" data-url="{{ route('toggle-dark-mode') }}" aria-label="{{ auth()->user()->dark_mode ? 'Zapnout světlý režim' : 'Zapnout tmavý režim' }}" aria-pressed="{{ auth()->user()->dark_mode ? 'true' : 'false' }}">{{ auth()->user()->dark_mode ? 'Světlý režim' : 'Tmavý režim' }}</button>
    <a href="{{ route('notifications.index') }}" class="notification-link" aria-label="Oznámení: {{ $notificationCount }} nepřečtených"><x-icon name="bell" />@if($notificationCount)<span>{{ $notificationCount }}</span>@endif</a>
  </div>
</header>
