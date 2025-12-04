<x-layout>
    <div class="space-y-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Výkazy</h1>

        <!-- Filtry -->
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm ring-1 ring-gray-900/5 dark:ring-gray-700 p-4">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <select name="month" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white">
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ request('month', now()->month) == $i ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create(null, $i)->locale('cs')->monthName }}
                        </option>
                    @endfor
                </select>

                <input type="number" name="year" value="{{ request('year', now()->year) }}" placeholder="Rok" 
                       class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white">

                @if(auth()->user()->isAdmin())
                    <select name="user_id" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white">
                        <option value="">Všichni uživatelé</option>
                        @foreach(\App\Models\User::all() as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                @endif

                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    Filtrovat
                </button>
            </form>
        </div>

        <!-- Celkový čas -->
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm ring-1 ring-gray-900/5 dark:ring-gray-700 p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Celkový čas za měsíc</h2>
            <p class="text-3xl font-bold text-indigo-600 dark:text-indigo-400">
                {{ floor($totalTime / 60) }}h {{ $totalTime % 60 }}m
            </p>
        </div>

        <!-- Záznamy -->
        <div class="space-y-4">
            @forelse($groupedEntries as $date => $entries)
                <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm ring-1 ring-gray-900/5 dark:ring-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        {{ \Carbon\Carbon::parse($date)->format('d.m.Y') }}
                    </h3>
                    <div class="space-y-2">
                        @foreach($entries as $entry)
                            <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-900 dark:text-white">
                                            @if($entry->task && $entry->task->project && $entry->task->project->client)
                                                {{ $entry->task->title }}
                                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                                    ({{ $entry->task->project->client->name }} → {{ $entry->task->project->name }})
                                                </span>
                                            @elseif($entry->task && $entry->task->project)
                                                {{ $entry->task->title }}
                                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                                    ({{ $entry->task->project->name }})
                                                </span>
                                            @else
                                                Obecný čas
                                            @endif
                                        </p>
                                        @if($entry->description)
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $entry->description }}</p>
                                        @endif
                                        @if($entry->start_time && $entry->end_time)
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                {{ \Carbon\Carbon::parse($entry->start_time)->format('H:i') }} - 
                                                {{ \Carbon\Carbon::parse($entry->end_time)->format('H:i') }}
                                            </p>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        <p class="font-medium text-gray-900 dark:text-white">
                                            {{ floor($entry->duration / 60) }}h {{ $entry->duration % 60 }}m
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $entry->user->name }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm ring-1 ring-gray-900/5 dark:ring-gray-700 p-12 text-center">
                    <p class="text-gray-500 dark:text-gray-400">Žádné záznamy</p>
                </div>
            @endforelse
        </div>
    </div>
</x-layout>

