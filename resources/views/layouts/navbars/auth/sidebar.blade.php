<aside class="workspace-sidebar" id="workspace-sidebar" aria-label="Hlavní navigace">
  <a class="workspace-brand" href="{{ route('dashboard') }}"><span class="brand-symbol" aria-hidden="true">c.</span><span>Codencio<small>Týmový prostor</small></span></a>
  <nav>
    @foreach([
      'Pracovní prostor' => [['dashboard','Nástěnka','home'],['work','Moje práce','tasks'],['projects','Projekty','folder'],['tasks','Úkoly','tasks']],
      'Evidence' => [['clients','Klienti','clients'],['support-plans','Služby a období','calendar'],['invoices','Faktury','invoice'],['passwords','Přístupy','key']],
    ] as $group => $items)
    <span class="nav-group-label">{{ $group }}</span>
    @foreach($items as [$path,$label,$icon])
    <a href="{{ $path === 'dashboard' ? route('dashboard') : route($path.'.index') }}" class="workspace-nav-link {{ ($path === 'work' ? Request::is('my-work*') : Request::is($path.'*')) ? 'active' : '' }}" @if(($path === 'work' ? Request::is('my-work*') : Request::is($path.'*'))) aria-current="page" @endif><x-icon :name="$icon" /><span>{{ $label }}</span></a>
    @endforeach
    @endforeach
    @if(auth()->user()->isAdmin())<span class="nav-group-label">Nastavení</span>
    <a href="{{ route('user-management') }}" class="workspace-nav-link {{ Request::is('user-management','users*') ? 'active' : '' }}"><x-icon name="people" /><span>Lidé a přístupy</span></a>@endif
  </nav>
  <div class="sidebar-person"><span class="person-avatar">{{ mb_substr(auth()->user()->name,0,1) }}</span><div><strong>{{ auth()->user()->name }}</strong><a href="{{ url('user-profile') }}">Můj profil</a></div><a class="sidebar-logout" href="{{ url('logout') }}" aria-label="Odhlásit" title="Odhlásit"><x-icon name="logout" /></a></div>
</aside>
<button type="button" class="workspace-backdrop" aria-label="Zavřít navigaci" hidden></button>
