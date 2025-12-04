<x-layout>
    <div class="max-w-2xl">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">Upravit úkol</h1>

        <form action="{{ route('tasks.update', $task) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Název *</label>
                <input type="text" name="title" id="title" value="{{ $task->title }}" required 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white">
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Popis</label>
                <textarea name="description" id="description" rows="4" 
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white">{{ $task->description }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                    <select name="status" id="status" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white">
                        <option value="todo" {{ $task->status === 'todo' ? 'selected' : '' }}>Udělat</option>
                        <option value="in_progress" {{ $task->status === 'in_progress' ? 'selected' : '' }}>Probíhá</option>
                        <option value="pending_approval" {{ $task->status === 'pending_approval' ? 'selected' : '' }}>Ke schválení</option>
                        <option value="done" {{ $task->status === 'done' ? 'selected' : '' }}>Dokončeno</option>
                    </select>
                </div>

                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Priorita</label>
                    <select name="priority" id="priority" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white">
                        <option value="low" {{ $task->priority === 'low' ? 'selected' : '' }}>Nízká</option>
                        <option value="medium" {{ $task->priority === 'medium' ? 'selected' : '' }}>Střední</option>
                        <option value="high" {{ $task->priority === 'high' ? 'selected' : '' }}>Vysoká</option>
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
                            <option value="{{ $user->id }}" {{ $task->assigned_to_id == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="due_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Datum splnění</label>
                    <input type="date" name="due_date" id="due_date" value="{{ $task->due_date ? $task->due_date->format('Y-m-d') : '' }}" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white">
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    Uložit
                </button>
                <a href="{{ route('tasks.show', $task) }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600">
                    Zrušit
                </a>
            </div>
        </form>
    </div>
</x-layout>

