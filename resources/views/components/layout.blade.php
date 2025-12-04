@props(['title' => 'CRM System'])

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }}</title>
    <script>
        // Načíst dark mode PŘED načtením stránky - zabrání probliknutí
        (function() {
            const saved = localStorage.getItem('darkMode');
            if (saved === 'true') {
                document.documentElement.classList.add('dark');
            } else if (saved === 'false') {
                document.documentElement.classList.remove('dark');
            } else {
                // Pokud není uloženo, použít systémové nastavení
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                if (prefersDark) {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('darkMode', 'true');
                } else {
                    localStorage.setItem('darkMode', 'false');
                }
            }
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-50 dark:bg-gray-950">
    @auth
        <div class="flex h-screen">
            @include('layouts.sidebar')
            
            <div class="flex flex-1 flex-col overflow-hidden bg-white dark:bg-gray-800">
                <!-- Top Bar -->
                <div class="h-16 bg-gray-900 border-b border-gray-800 flex items-center justify-between px-6">
                    <div class="flex-1"></div>
                    <div class="flex items-center gap-4" id="top-bar-components">
                        @livewire('work-time-tracker', key('work-time-tracker-' . auth()->id()))
                        @livewire('notifications-dropdown', key('notifications-dropdown-' . auth()->id()))
                    </div>
                </div>
                
                <!-- Main Content -->
                <div class="flex-1 overflow-y-auto">
                    <div class="p-6">
                        @if(session('success'))
                            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                                {{ session('success') }}
                            </div>
                        @endif
                        
                        @if(session('error'))
                            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                                {{ session('error') }}
                            </div>
                        @endif
                        
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Guest Layout -->
        <div class="min-h-screen">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif
            
            {{ $slot }}
        </div>
    @endauth
    @livewireScripts
    <script>
        // Funkce pro načtení dark mode z localStorage
        function loadDarkMode() {
            const saved = localStorage.getItem('darkMode');
            if (saved === 'true') {
                document.documentElement.classList.add('dark');
                return true;
            } else if (saved === 'false') {
                document.documentElement.classList.remove('dark');
                return false;
            }
            return document.documentElement.classList.contains('dark');
        }
        
        // Globální dark mode funkce - dostupné na všech stránkách
        window.toggleDarkMode = function() {
            const html = document.documentElement;
            const isDark = html.classList.contains('dark');
            const newDarkMode = !isDark;
            
            if (newDarkMode) {
                html.classList.add('dark');
                localStorage.setItem('darkMode', 'true');
            } else {
                html.classList.remove('dark');
                localStorage.setItem('darkMode', 'false');
            }
            
            // Aktualizovat UI
            updateDarkModeUI(newDarkMode);
        };
        
        window.updateDarkModeUI = function(isDark) {
            const darkIcon = document.getElementById('dark-mode-icon');
            const lightIcon = document.getElementById('light-mode-icon');
            const text = document.getElementById('dark-mode-text');
            
            if (darkIcon && lightIcon && text) {
                if (isDark) {
                    darkIcon.classList.add('hidden');
                    lightIcon.classList.remove('hidden');
                    text.textContent = 'Světlý režim';
                } else {
                    darkIcon.classList.remove('hidden');
                    lightIcon.classList.add('hidden');
                    text.textContent = 'Tmavý režim';
                }
            }
        };
        
        // Aktualizovat UI při načtení stránky nebo navigaci
        function initDarkModeUI() {
            const isDark = loadDarkMode();
            updateDarkModeUI(isDark);
        }
        
        // Spustit při načtení DOM
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initDarkModeUI);
        } else {
            initDarkModeUI();
        }
        
        // Při Livewire navigaci znovu načíst dark mode a aktualizovat UI
        document.addEventListener('livewire:navigating', () => {
            loadDarkMode();
        });
        
        document.addEventListener('livewire:navigated', () => {
            initDarkModeUI();
            
            const topBar = document.getElementById('top-bar-components');
            if (topBar) {
                topBar.style.opacity = '1';
            }
        });
        
        // Zabránit probliknutí při navigaci
        document.addEventListener('livewire:navigating', () => {
            const topBar = document.getElementById('top-bar-components');
            if (topBar) {
                topBar.style.opacity = '0.9';
            }
        });
    </script>
</body>
</html>

