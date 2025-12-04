<x-layout>
    <div class="max-w-2xl">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">Upravit heslo</h1>

        <form action="{{ route('passwords.update', $password) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Název *</label>
                <input type="text" name="title" id="title" value="{{ $password->title }}" required 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white">
            </div>

            <div>
                <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Uživatelské jméno *</label>
                <input type="text" name="username" id="username" value="{{ $password->username }}" required 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Heslo *</label>
                <input type="text" name="password" id="password" value="{{ $password->password }}" required 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white">
            </div>

            <div>
                <label for="url" class="block text-sm font-medium text-gray-700 dark:text-gray-300">URL</label>
                <input type="url" name="url" id="url" value="{{ $password->url }}" 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white">
            </div>

            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Poznámky</label>
                <textarea name="notes" id="notes" rows="4" 
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white">{{ $password->notes }}</textarea>
            </div>

            @if(auth()->user()->isAdmin())
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Sdílet s uživateli</label>
                    <div class="space-y-2">
                        @foreach($users as $user)
                            <label class="flex items-center">
                                <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" 
                                       {{ $password->users->contains($user->id) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ $user->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="flex gap-3">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    Uložit
                </button>
                <a href="{{ route('passwords.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600">
                    Zrušit
                </a>
            </div>
        </form>
    </div>
</x-layout>

