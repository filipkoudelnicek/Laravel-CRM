<x-layout>
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Hesla</h1>
            <button onclick="document.getElementById('createModal').classList.remove('hidden')" 
                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                Přidat heslo
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($passwords as $password)
                <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm ring-1 ring-gray-900/5 dark:ring-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ $password->title }}</h3>
                    <dl class="space-y-2 text-sm">
                        <div>
                            <dt class="text-gray-500 dark:text-gray-400">Uživatelské jméno</dt>
                            <dd class="text-gray-900 dark:text-white font-mono">{{ $password->username }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 dark:text-gray-400">Heslo</dt>
                            <dd class="text-gray-900 dark:text-white font-mono">{{ $password->password }}</dd>
                        </div>
                        @if($password->url)
                            <div>
                                <dt class="text-gray-500 dark:text-gray-400">URL</dt>
                                <dd class="text-gray-900 dark:text-white">
                                    <a href="{{ $password->url }}" target="_blank" class="text-indigo-600 hover:text-indigo-900">
                                        {{ $password->url }}
                                    </a>
                                </dd>
                            </div>
                        @endif
                    </dl>
                    <div class="mt-4 flex gap-2">
                        <button onclick="editPassword({{ $password->id }})" 
                                class="text-sm text-indigo-600 hover:text-indigo-900">
                            Upravit
                        </button>
                        <form action="{{ route('passwords.destroy', $password) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm text-red-600 hover:text-red-900" 
                                    onclick="return confirm('Opravdu chcete smazat toto heslo?')">
                                Smazat
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white dark:bg-gray-900 rounded-xl shadow-sm ring-1 ring-gray-900/5 dark:ring-gray-700 p-12 text-center">
                    <p class="text-gray-500 dark:text-gray-400">Žádná hesla</p>
                </div>
            @endforelse
        </div>
    </div>
</x-layout>

