<x-layout>
    <div class="max-w-2xl">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">Vytvořit projekt</h1>

        <form action="{{ route('projects.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Název *</label>
                <input type="text" name="name" id="name" required 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white">
            </div>

            <div>
                <label for="client_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Klient *</label>
                <select name="client_id" id="client_id" required 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white">
                    <option value="">Vyberte klienta</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}">{{ $client->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Popis</label>
                <textarea name="description" id="description" rows="4" 
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white"></textarea>
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                <select name="status" id="status" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white">
                    <option value="active">Aktivní</option>
                    <option value="inactive">Neaktivní</option>
                </select>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    Vytvořit
                </button>
                <a href="{{ route('projects.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600">
                    Zrušit
                </a>
            </div>
        </form>
    </div>
</x-layout>

