<div class="grid grid-cols-4 gap-4">
    @foreach(['todo' => 'Udělat', 'in_progress' => 'Probíhá', 'pending_approval' => 'Ke schválení', 'done' => 'Dokončeno'] as $status => $label)
        <div class="bg-gray-100 dark:bg-gray-800 rounded-lg p-4">
            <h3 class="font-semibold text-gray-900 dark:text-white mb-4">{{ $label }}</h3>
            <div class="space-y-2" 
                 x-data="{ 
                     tasks: @js($tasks[$status] ?? []),
                     status: '{{ $status }}'
                 }"
                 @drop.prevent="
                     const taskId = $event.dataTransfer.getData('taskId');
                     if (taskId) {
                         $wire.updateTaskStatus(taskId, status);
                     }
                 "
                 @dragover.prevent>
                @if(isset($tasks[$status]))
                    @foreach($tasks[$status] as $task)
                        <div draggable="true" 
                             @dragstart="$event.dataTransfer.setData('taskId', '{{ $task['id'] }}')"
                             class="bg-white dark:bg-gray-900 p-3 rounded-lg shadow cursor-move hover:shadow-md">
                            <p class="font-medium text-gray-900 dark:text-white">{{ $task['title'] }}</p>
                            @if($task['assigned_to'])
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $task['assigned_to']['name'] }}</p>
                            @endif
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    @endforeach
</div>

