<x-layout>
    <div class="space-y-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Dashboard</h1>

        <!-- Statistiky -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm ring-1 ring-gray-900/5 dark:ring-gray-700 p-6">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Celkem úkolů</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $totalTasks }}</p>
            </div>

            <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm ring-1 ring-gray-900/5 dark:ring-gray-700 p-6">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Projekty</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $totalProjects }}</p>
            </div>

            <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm ring-1 ring-gray-900/5 dark:ring-gray-700 p-6">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Klienti</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $totalClients }}</p>
            </div>

            <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm ring-1 ring-gray-900/5 dark:ring-gray-700 p-6">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Trackovaný čas (měsíc)</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">
                    {{ floor($thisMonthTime / 60) }}h {{ $thisMonthTime % 60 }}m
                </p>
            </div>
        </div>

        <!-- Úkoly podle statusu -->
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm ring-1 ring-gray-900/5 dark:ring-gray-700 p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Úkoly podle statusu</h2>
            <div class="space-y-4">
                @foreach(['todo' => 'Udělat', 'in_progress' => 'Probíhá', 'pending_approval' => 'Ke schválení', 'done' => 'Dokončeno'] as $status => $label)
                    @php
                        $count = $tasksByStatus[$status] ?? 0;
                        $percentage = $totalTasks > 0 ? ($count / $totalTasks) * 100 : 0;
                    @endphp
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $label }}</span>
                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ $count }}</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Overdue úkoly -->
        @if($overdueTasks->count() > 0)
            <div class="bg-red-50 dark:bg-red-900/20 rounded-xl shadow-sm ring-1 ring-red-900/5 dark:ring-red-700 p-6">
                <h2 class="text-xl font-semibold text-red-900 dark:text-red-400 mb-4">Zpožděné úkoly</h2>
                <div class="space-y-2">
                    @foreach($overdueTasks as $task)
                        <a href="{{ route('tasks.show', $task) }}" 
                           class="block p-3 bg-white dark:bg-gray-800 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                            <p class="font-medium text-gray-900 dark:text-white">{{ $task->title }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                @if($task->project && $task->project->client)
                                    {{ $task->project->client->name }} → {{ $task->project->name }}
                                @elseif($task->project)
                                    {{ $task->project->name }}
                                @else
                                    Bez projektu
                                @endif
                            </p>
                            <p class="text-xs text-red-600 dark:text-red-400 mt-1">
                                Splatnost: {{ $task->due_date->format('d.m.Y') }}
                            </p>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Rychlé akce -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('tasks.create') }}" 
               class="bg-indigo-600 text-white rounded-lg p-4 hover:bg-indigo-700 transition-colors text-center font-medium">
                Vytvořit úkol
            </a>
            <a href="{{ route('clients.create') }}" 
               class="bg-green-600 text-white rounded-lg p-4 hover:bg-green-700 transition-colors text-center font-medium">
                Přidat klienta
            </a>
            <a href="{{ route('projects.create') }}" 
               class="bg-blue-600 text-white rounded-lg p-4 hover:bg-blue-700 transition-colors text-center font-medium">
                Vytvořit projekt
            </a>
        </div>
    </div>
</x-layout>

