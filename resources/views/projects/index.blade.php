<x-layout>
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Projekty</h1>
            <a href="{{ route('projects.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                Vytvořit projekt
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($projects as $project)
                <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm ring-1 ring-gray-900/5 dark:ring-gray-700 p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                <a href="{{ route('projects.show', $project) }}" class="hover:text-indigo-600">
                                    {{ $project->name }}
                                </a>
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                {{ $project->client->name ?? 'Bez klienta' }}
                            </p>
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full 
                            {{ $project->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $project->status === 'active' ? 'Aktivní' : 'Neaktivní' }}
                        </span>
                    </div>
                    
                    @if($project->description)
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">{{ Str::limit($project->description, 100) }}</p>
                    @endif

                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $project->tasks->count() }} úkolů
                        </span>
                        <div class="flex gap-2">
                            <a href="{{ route('projects.edit', $project) }}" class="text-sm text-indigo-600 hover:text-indigo-900">Upravit</a>
                            <form action="{{ route('projects.destroy', $project) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm text-red-600 hover:text-red-900" onclick="return confirm('Opravdu chcete smazat tento projekt?')">Smazat</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white dark:bg-gray-900 rounded-xl shadow-sm ring-1 ring-gray-900/5 dark:ring-gray-700 p-12 text-center">
                    <p class="text-gray-500 dark:text-gray-400">Žádné projekty</p>
                </div>
            @endforelse
        </div>
    </div>
</x-layout>

