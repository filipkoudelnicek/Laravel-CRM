<x-layout>
    <div class="max-w-2xl">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">Upravit klienta</h1>

        <form action="{{ route('clients.update', $client) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Název *</label>
                <input type="text" name="name" id="name" value="{{ $client->name }}" required 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white">
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                <input type="email" name="email" id="email" value="{{ $client->email }}" 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white">
            </div>

            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Telefon</label>
                <input type="text" name="phone" id="phone" value="{{ $client->phone }}" 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white">
            </div>

            <div>
                <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Adresa</label>
                <textarea name="address" id="address" rows="3" 
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white">{{ $client->address }}</textarea>
            </div>

            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Poznámky</label>
                <textarea name="notes" id="notes" rows="4" 
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white">{{ $client->notes }}</textarea>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    Uložit
                </button>
                <a href="{{ route('clients.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600">
                    Zrušit
                </a>
            </div>
        </form>
    </div>
</x-layout>

