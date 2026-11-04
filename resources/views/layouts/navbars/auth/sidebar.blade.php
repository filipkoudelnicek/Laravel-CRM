<aside class="workspace-sidebar" id="workspace-sidebar" aria-label="Hlavní navigace">
  <a class="workspace-brand" href="{{ route('dashboard') }}"><span class="brand-symbol" aria-hidden="true">c.</span><span>Codencio<small>Pracovní prostor</small></span></a>
  <nav>
    @foreach([
      ['dashboard','Moje práce','fa-house'], ['projects','Projekty','fa-layer-group'],
      ['tasks','Úkoly','fa-list-check'], ['clients','Klienti','fa-building'],
      ['support-plans','Služby a období','fa-calendar-check'], ['invoices','Faktury','fa-file-invoice'],
      ['passwords','Přístupy','fa-key'], ['notifications','Oznámení','fa-bell']
    ] as [$path,$label,$icon])
    <a href="{{ $path === 'dashboard' ? route('dashboard') : route($path . '.index') }}" class="workspace-nav-link {{ Request::is($path . '*') ? 'active' : '' }}" @if(Request::is($path . '*')) aria-current="page" @endif><i class="fa-solid {{ $icon }}" aria-hidden="true"></i><span>{{ $label }}</span></a>
    @endforeach
    @if(auth()->user()->isAdmin())<a href="{{ route('user-management') }}" class="workspace-nav-link {{ Request::is('user-management','users*') ? 'active' : '' }}"><i class="fa-solid fa-users" aria-hidden="true"></i><span>Lidé a přístupy</span></a>@endif
  </nav>
  <div class="sidebar-person"><span class="person-avatar">{{ mb_substr(auth()->user()->name,0,1) }}</span><div><strong>{{ auth()->user()->name }}</strong><a href="{{ url('user-profile') }}">Můj profil</a></div></div>
</aside>
<button type="button" class="workspace-backdrop" aria-label="Zavřít navigaci" hidden></button>
