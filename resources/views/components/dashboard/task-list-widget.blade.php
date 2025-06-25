@props(['tasks', 'title'])

<div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg h-full">
    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-4">{{ $title }}</h3>
    @if($tasks->count() > 0)
    <ul class="space-y-3">
        @foreach($tasks as $task)
        <li class="flex items-center justify-between p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
            <span class="text-sm text-gray-700 dark:text-gray-300">{{ $task->title }}</span>
            <div class="flex items-center gap-2">
                <span class="text-xs text-gray-400 dark:text-gray-500">{{ \Carbon\Carbon::parse($task->due_date)->format('d M') }}</span>
                <a href="{{ route('tasks.edit', $task) }}" class="text-indigo-500 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 text-xs">View</a>
            </div>
        </li>
        @endforeach
    </ul>
    @else
    <p class="text-center text-sm text-gray-500 dark:text-gray-400 py-8">No upcoming tasks. Good job!</p>
    @endif
</div>