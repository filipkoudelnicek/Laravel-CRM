<div class="relative">
    <button wire:click="toggleDropdown" 
            class="relative p-2 text-gray-300 hover:text-white focus:outline-none rounded-lg hover:bg-gray-800 transition-colors">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        @if($unreadCount > 0)
            <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-500 ring-2 ring-gray-900"></span>
        @endif
    </button>

    @if($showDropdown)
        <div class="absolute right-0 mt-2 w-96 bg-white dark:bg-gray-800 rounded-lg shadow-lg z-20 border border-gray-200 dark:border-gray-700">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Notifikace</h3>
                @if($unreadCount > 0)
                    <button wire:click="markAllAsRead" 
                            class="text-sm text-indigo-600 hover:text-indigo-800 dark:text-indigo-400">
                        Označit vše jako přečtené
                    </button>
                @endif
            </div>
            <div class="max-h-96 overflow-y-auto">
                @if(count($notifications) === 0)
                    <div class="p-4 text-center text-gray-500 dark:text-gray-400">
                        Žádné notifikace
                    </div>
                @else
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($notifications as $notification)
                            <a href="{{ $notification['task_id'] ? route('tasks.show', $notification['task_id']) : '#' }}" 
                               wire:click="markAsRead({{ $notification['id'] }})"
                               class="block p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors {{ !$notification['read'] ? 'bg-indigo-50 dark:bg-indigo-900/20' : '' }}">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <p class="text-sm font-medium {{ !$notification['read'] ? 'text-gray-900 dark:text-white' : 'text-gray-700 dark:text-gray-300' }}">
                                            {{ $notification['title'] }}
                                        </p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                            {{ $notification['message'] }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-500 mt-2">
                                            {{ \Carbon\Carbon::parse($notification['created_at'])->diffForHumans() }}
                                        </p>
                                    </div>
                                    @if(!$notification['read'])
                                        <span class="ml-2 h-2 w-2 rounded-full bg-indigo-600 flex-shrink-0 mt-1"></span>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>

