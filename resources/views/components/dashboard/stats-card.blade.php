@props(['title', 'count', 'icon', 'tasks'])

<div x-data="{ open: false }" class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg transition-all duration-300 overflow-hidden">
    <button @click="open = !open" class="w-full p-6 text-left focus:outline-none hover:bg-gray-50 dark:hover:bg-gray-700/50" @disabled($count === 0)>
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">{{ $title }}</p>
                <p class="mt-1 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $count }}</p>
            </div>
            <div class="flex-shrink-0 flex items-center justify-center bg-indigo-100 dark:bg-indigo-900/50 rounded-full p-3">
                <svg class="h-6 w-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    {!! $icon !!}
                </svg>
                {{-- যদি টাস্ক থাকে তাহলেই ড্রপডাউন অ্যারো দেখানো হবে --}}
                @if($count > 0)
                    <svg :class="{'rotate-180': open}" class="ms-2 h-4 w-4 text-gray-400 transform transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                @endif
            </div>
        </div>
    </button>

    {{-- Expandable Content: ক্লিক করলে এই অংশটি খুলবে --}}
    <div x-show="open" x-collapse>
        <div class="px-6 pb-6 pt-2">
            @if($tasks->isNotEmpty())
                <ul class="space-y-2 max-h-48 overflow-y-auto">
                    @foreach($tasks as $task)
                        <li class="flex justify-between items-center text-sm p-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700/50">
                            {{-- টাস্কের টাইটেল এবং ডিউ ডেট --}}
                            <div class="flex-grow">
                                <p class="text-gray-700 dark:text-gray-300">{{ $task->title }}</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500">Due: {{ \Carbon\Carbon::parse($task->due_date)->diffForHumans() }}</p>
                            </div>

                            {{-- অ্যাকশন বাটনগুলো --}}
                            <div class="flex items-center gap-3 flex-shrink-0 ml-2">
                                {{-- টাস্কটি যদি সম্পন্ন না হয়ে থাকে, তাহলে 'Complete' বাটন দেখাবে --}}
                                @if(!$task->is_completed)
                                    <form action="{{ route('tasks.toggleComplete', $task) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-xs text-green-500 hover:underline">Complete</button>
                                    </form>
                                @endif
                                {{-- নতুন 'View' বাটন --}}
                                <a href="{{ route('tasks.edit', $task) }}" class="text-xs text-indigo-500 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300">View</a>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-sm text-center text-gray-500 py-4">No tasks in this category.</p>
            @endif
        </div>
    </div>
</div>
