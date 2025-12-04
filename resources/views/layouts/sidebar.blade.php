<div class="flex h-screen w-64 flex-col bg-gray-900">
    <div class="flex h-16 shrink-0 items-center px-6 border-b border-gray-800">
        <h1 class="text-xl font-bold text-white">CRM System</h1>
    </div>
    
    <nav class="flex flex-1 flex-col gap-1 px-3 py-4">
        <a href="{{ route('dashboard') }}" 
           wire:navigate
           class="group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('dashboard*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            Dashboard
        </a>
        
        <a href="{{ route('clients.index') }}" 
           wire:navigate
           class="group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('clients*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            Klienti
        </a>
        
        <a href="{{ route('projects.index') }}" 
           wire:navigate
           class="group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('projects*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
            </svg>
            Projekty
        </a>
        
        <a href="{{ route('tasks.index') }}" 
           wire:navigate
           class="group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('tasks*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            Úkoly
        </a>
        
        <a href="{{ route('time-tracking.index') }}" 
           wire:navigate
           class="group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('time-tracking*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Pracovní doba
        </a>
        
        <a href="{{ route('reports.index') }}" 
           wire:navigate
           class="group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('reports*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            Výkazy
        </a>
        
        <a href="{{ route('passwords.index') }}" 
           wire:navigate
           class="group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('passwords*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
            </svg>
            Hesla
        </a>
    </nav>
    
    <div class="border-t border-gray-800 p-4 space-y-2">
        <button onclick="toggleDarkMode()" 
                class="group flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-gray-300 transition-colors hover:bg-gray-800 hover:text-white">
            <svg id="dark-mode-icon" class="h-5 w-5 shrink-0 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
            </svg>
            <svg id="light-mode-icon" class="h-5 w-5 shrink-0 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <span id="dark-mode-text">Tmavý režim</span>
        </button>
        
        @auth
            <div class="px-3 py-2">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Uživatel</p>
                <p class="mt-1 text-sm font-medium text-white">{{ auth()->user()->name ?? 'Uživatel' }}</p>
                <p class="text-xs text-gray-400">{{ auth()->user()->role ?? 'USER' }}</p>
            </div>
            
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" 
                        class="group flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-gray-300 transition-colors hover:bg-gray-800 hover:text-white">
                    <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Odhlásit se
                </button>
            </form>
        @endauth
    </div>
</div>

