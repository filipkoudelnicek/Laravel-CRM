<x-layout>
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Úkoly</h1>
            <a href="{{ route('tasks.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                Vytvořit úkol
            </a>
        </div>

        <!-- Filtry -->
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm ring-1 ring-gray-900/5 dark:ring-gray-700 p-4">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Vyhledat..." 
                       class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white">
                
                <select name="status" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white">
                    <option value="">Všechny statusy</option>
                    <option value="todo" {{ request('status') === 'todo' ? 'selected' : '' }}>Udělat</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>Probíhá</option>
                    <option value="pending_approval" {{ request('status') === 'pending_approval' ? 'selected' : '' }}>Ke schválení</option>
                    <option value="done" {{ request('status') === 'done' ? 'selected' : '' }}>Dokončeno</option>
                </select>

                <select name="priority" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white">
                    <option value="">Všechny priority</option>
                    <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Nízká</option>
                    <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Střední</option>
                    <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>Vysoká</option>
                </select>

                <select name="project_id" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white">
                    <option value="">Všechny projekty</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                            @if($project->client)
                                {{ $project->client->name }} - {{ $project->name }}
                            @else
                                {{ $project->name }}
                            @endif
                        </option>
                    @endforeach
                </select>

                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        Filtrovat
                    </button>
                    <a href="{{ route('tasks.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Přepínání zobrazení -->
        <div class="flex gap-2">
            <a href="{{ route('tasks.index', ['view' => 'list'] + request()->except('view')) }}" 
               class="px-4 py-2 {{ $view === 'list' ? 'bg-indigo-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }} rounded-lg">
                Seznam
            </a>
            <a href="{{ route('tasks.index', ['view' => 'kanban'] + request()->except('view')) }}" 
               class="px-4 py-2 {{ $view === 'kanban' ? 'bg-indigo-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }} rounded-lg">
                Kanban
            </a>
        </div>

        @if($view === 'kanban')
            @livewire('task-kanban')
        @else
            <!-- List View -->
            <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm ring-1 ring-gray-900/5 dark:ring-gray-700 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Název</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Projekt</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Priorita</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Přiřazeno</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Akce</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($tasks as $task)
                            <tr>
                                <td class="px-6 py-4">
                                    <a href="{{ route('tasks.show', $task) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-900">
                                        {{ $task->title }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    @if($task->project && $task->project->client)
                                        {{ $task->project->client->name }} → {{ $task->project->name }}
                                    @elseif($task->project)
                                        {{ $task->project->name }}
                                    @else
                                        Bez projektu
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        {{ $task->status === 'done' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $task->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $task->status === 'todo' ? 'bg-gray-100 text-gray-800' : '' }}
                                        {{ $task->status === 'pending_approval' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                                        {{ ['todo' => 'Udělat', 'in_progress' => 'Probíhá', 'pending_approval' => 'Ke schválení', 'done' => 'Dokončeno'][$task->status] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ ucfirst($task->priority) }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $task->assignedTo->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-medium">
                                    <a href="{{ route('tasks.edit', $task) }}" class="text-indigo-600 hover:text-indigo-900">Upravit</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                    Žádné úkoly
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-layout>

