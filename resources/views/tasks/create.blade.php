<x-layout>
    <div class="max-w-2xl">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">Vytvořit úkol</h1>

        <form action="{{ route('tasks.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Název *</label>
                <input type="text" name="title" id="title" required 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white">
            </div>

            <div>
                <label for="project_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Projekt *</label>
                <select name="project_id" id="project_id" required 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white">
                    <option value="">Vyberte projekt</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                            @if($project->client)
                                {{ $project->client->name }} → {{ $project->name }}
                            @else
                                {{ $project->name }}
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Popis</label>
                <textarea name="description" id="description" rows="4" 
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                    <select name="status" id="status" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white">
                        <option value="todo">Udělat</option>
                        <option value="in_progress">Probíhá</option>
                        <option value="pending_approval">Ke schválení</option>
                        <option value="done">Dokončeno</option>
                    </select>
                </div>

                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Priorita</label>
                    <select name="priority" id="priority" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white">
                        <option value="low">Nízká</option>
                        <option value="medium" selected>Střední</option>
                        <option value="high">Vysoká</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="assigned_to_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Přiřazeno</label>
                    <select name="assigned_to_id" id="assigned_to_id" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white">
                        <option value="">Nepřiřazeno</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="due_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Datum splnění</label>
                    <input type="date" name="due_date" id="due_date" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white">
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    Vytvořit
                </button>
                <a href="{{ route('tasks.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600">
                    Zrušit
                </a>
            </div>
        </form>
    </div>
</x-layout>

