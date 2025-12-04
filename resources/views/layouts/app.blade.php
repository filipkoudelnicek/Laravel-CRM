<!DOCTYPE html>
<html lang="cs" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" 
      x-bind:class="{ 'dark': darkMode }"
      x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CRM System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-50 dark:bg-gray-950">
    <div class="flex h-screen">
        @include('layouts.sidebar')
        
        <div class="flex flex-1 flex-col overflow-hidden bg-white dark:bg-gray-800">
            <!-- Top Bar -->
            <div class="h-16 bg-gray-900 border-b border-gray-800 flex items-center justify-between px-6">
                <div class="flex-1"></div>
                <div class="flex items-center gap-4">
                    @livewire('work-time-tracker')
                    @livewire('notifications-dropdown')
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
    @livewireScripts
</body>
</html>

