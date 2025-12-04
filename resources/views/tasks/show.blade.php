<x-layout>
    <div class="space-y-6">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $task->title }}</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">
                    @if($task->project && $task->project->client)
                        {{ $task->project->client->name }} → {{ $task->project->name }}
                    @elseif($task->project)
                        {{ $task->project->name }}
                    @else
                        Bez projektu
                    @endif
                </p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('tasks.edit', $task) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    Upravit
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <!-- Popis -->
                @if($task->description)
                    <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm ring-1 ring-gray-900/5 dark:ring-gray-700 p-6">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Popis</h2>
                        <p class="text-gray-700 dark:text-gray-300">{{ $task->description }}</p>
                    </div>
                @endif

                <!-- Komentáře -->
                <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm ring-1 ring-gray-900/5 dark:ring-gray-700 p-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Komentáře</h2>
                    
                    <form action="{{ route('comments.store', $task) }}" method="POST" enctype="multipart/form-data" class="mb-6">
                        @csrf
                        <textarea name="content" rows="4" required 
                                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white"
                                  placeholder="Napište komentář... Můžete použít @username pro zmínění uživatele."></textarea>
                        <div class="mt-2 flex justify-between items-center">
                            <input type="file" name="file" class="text-sm text-gray-700 dark:text-gray-300">
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                                Přidat komentář
                            </button>
                        </div>
                    </form>

                    @php
                        $mentionService = app(\App\Services\MentionService::class);
                    @endphp
                    <div class="space-y-4">
                        @forelse($task->comments as $comment)
                            <div class="border-l-4 border-indigo-500 pl-4">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $comment->user->name ?? 'Neznámý uživatel' }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $comment->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>
                                <div class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">
                                    {!! $mentionService->highlightMentions($comment->content) !!}
                                </div>
                                @if($comment->attachments->count() > 0)
                                    <div class="mt-2 space-y-1">
                                        @foreach($comment->attachments as $attachment)
                                            <a href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank" 
                                               class="text-sm text-indigo-600 hover:text-indigo-900 flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                                </svg>
                                                {{ $attachment->file_name }}
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                                
                                <!-- Odpovědi -->
                                @if($comment->replies->count() > 0)
                                    <div class="mt-4 ml-4 space-y-3">
                                        @foreach($comment->replies as $reply)
                                            <div class="border-l-2 border-gray-300 dark:border-gray-600 pl-3">
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $reply->user->name ?? 'Neznámý uživatel' }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $reply->created_at->diffForHumans() }}</p>
                                                <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">
                                                    {!! $mentionService->highlightMentions($reply->content) !!}
                                                </p>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @empty
                            <p class="text-gray-500 dark:text-gray-400">Žádné komentáře</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm ring-1 ring-gray-900/5 dark:ring-gray-700 p-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Detaily</h2>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                            <dd>
                                <span class="px-2 py-1 text-xs rounded-full 
                                    {{ $task->status === 'done' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $task->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $task->status === 'todo' ? 'bg-gray-100 text-gray-800' : '' }}
                                    {{ $task->status === 'pending_approval' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                                    {{ ['todo' => 'Udělat', 'in_progress' => 'Probíhá', 'pending_approval' => 'Ke schválení', 'done' => 'Dokončeno'][$task->status] }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Priorita</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">{{ ucfirst($task->priority) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Přiřazeno</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">{{ $task->assignedTo->name ?? 'Nepřiřazeno' }}</dd>
                        </div>
                        @if($task->due_date)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Datum splnění</dt>
                                <dd class="text-sm text-gray-900 dark:text-white">{{ $task->due_date->format('d.m.Y') }}</dd>
                            </div>
                        @endif
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Vytvořil</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">{{ $task->createdBy->name ?? 'Neznámý uživatel' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</x-layout>

