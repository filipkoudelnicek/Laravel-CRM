<x-layout>
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $client->name }}</h1>
            <div class="flex gap-3">
                <a href="{{ route('clients.edit', $client) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    Upravit
                </a>
                <form action="{{ route('clients.destroy', $client) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700" onclick="return confirm('Opravdu chcete smazat tohoto klienta?')">
                        Smazat
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm ring-1 ring-gray-900/5 dark:ring-gray-700 p-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Informace</h2>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</dt>
                        <dd class="text-sm text-gray-900 dark:text-white">{{ $client->email ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Telefon</dt>
                        <dd class="text-sm text-gray-900 dark:text-white">{{ $client->phone ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Adresa</dt>
                        <dd class="text-sm text-gray-900 dark:text-white">{{ $client->address ?? '-' }}</dd>
                    </div>
                    @if($client->notes)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Poznámky</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">{{ $client->notes }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm ring-1 ring-gray-900/5 dark:ring-gray-700 p-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Projekty</h2>
                @if($client->projects->count() > 0)
                    <div class="space-y-2">
                        @foreach($client->projects as $project)
                            <a href="{{ route('projects.show', $project) }}" 
                               class="block p-3 bg-gray-50 dark:bg-gray-800 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                                <p class="font-medium text-gray-900 dark:text-white">{{ $project->name }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $project->status }}</p>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400">Žádné projekty</p>
                @endif
            </div>
        </div>
    </div>
</x-layout>

