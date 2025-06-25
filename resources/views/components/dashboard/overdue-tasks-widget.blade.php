<!-- @props(['tasks'])

<div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg h-full">
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200">Overdue Tasks</h3>
        @if($tasks->count() > 0)
            <span class="bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-full dark:bg-red-900 dark:text-red-300">{{ $tasks->count() }}</span>
        @endif
    </div>
    
    @if($tasks->count() > 0)
    <ul class="space-y-3 mt-4 max-h-60 overflow-y-auto">
        @foreach($tasks as $task)
        <li class="flex items-center justify-between p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
            <div class="flex-grow">
                <p class="text-sm text-gray-700 dark:text-gray-300">{{ $task->title }}</p>
                <p class="text-xs text-gray-400 dark:text-gray-500">Due: {{ \Carbon\Carbon::parse($task->due_date)->diffForHumans() }}</p>
            </div>
            <div class="flex items-center gap-2">
                <form action="{{ route('tasks.toggleComplete', $task) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="text-xs text-green-500 hover:underline">Complete</button>
                </form>
            </div>
        </li>
        @endforeach
    </ul>
    @else
    <p class="text-center text-sm text-gray-500 dark:text-gray-400 py-8">No overdue tasks. You are all caught up!</p>
    @endif
</div> -->