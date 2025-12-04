<x-layout>
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $project->name }}</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">{{ $project->client->name ?? 'Bez klienta' }}</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('projects.edit', $project) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    Upravit
                </a>
                <form action="{{ route('projects.destroy', $project) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700" onclick="return confirm('Opravdu chcete smazat tento projekt?')">
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
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                        <dd class="text-sm text-gray-900 dark:text-white">
                            <span class="px-2 py-1 text-xs rounded-full {{ $project->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $project->status === 'active' ? 'Aktivní' : 'Neaktivní' }}
                            </span>
                        </dd>
                    </div>
                    @if($project->description)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Popis</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">{{ $project->description }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm ring-1 ring-gray-900/5 dark:ring-gray-700 p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Úkoly</h2>
                    <a href="{{ route('tasks.create', ['project_id' => $project->id]) }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                        Přidat úkol
                    </a>
                </div>
                @if($project->tasks->count() > 0)
                    <div class="space-y-2">
                        @foreach($project->tasks as $task)
                            <a href="{{ route('tasks.show', $task) }}" 
                               class="block p-3 bg-gray-50 dark:bg-gray-800 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                                <p class="font-medium text-gray-900 dark:text-white">{{ $task->title }}</p>
                                <div class="flex gap-2 mt-1">
                                    <span class="text-xs px-2 py-1 rounded-full 
                                        {{ $task->status === 'done' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $task->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $task->status === 'todo' ? 'bg-gray-100 text-gray-800' : '' }}
                                        {{ $task->status === 'pending_approval' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                                        {{ ['todo' => 'Udělat', 'in_progress' => 'Probíhá', 'pending_approval' => 'Ke schválení', 'done' => 'Dokončeno'][$task->status] }}
                                    </span>
                                    @if($task->assignedTo)
                                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $task->assignedTo->name ?? 'Nepřiřazeno' }}</span>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400">Žádné úkoly</p>
                @endif
            </div>
        </div>
    </div>
</x-layout>

