<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('My Tasks') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="flex items-center justify-end mb-6">
                        <a href="{{ route('tasks.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            Create Task
                        </a>
                    </div>
                    
                    @if(session('success'))
                        <div class="bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-200 px-4 py-3 rounded mb-4" role="alert">
                            <p>{{ session('success') }}</p>
                        </div>
                    @endif

                    @if($tasks->count())
                        <div class="overflow-x-auto">
                            <table class="table-auto w-full">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-4 py-3 text-left w-2/5">Title</th>
                                        <th class="px-4 py-3 text-left">Priority</th>
                                        <th class="px-4 py-3 text-left">Status</th>
                                        <th class="px-4 py-3 text-left">Due Date</th>
                                        <th class="px-4 py-3 text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tasks as $task)
                                        {{-- প্রতিটি প্যারেন্ট টাস্কের জন্য একটি নতুন Alpine.js কম্পোনেন্ট --}}
                                        <tbody x-data="{ open: false }">
                                            {{-- প্যারেন্ট টাস্কের সারি --}}
                                            <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-900/50">
                                                <td class="px-4 py-3">
                                                    <div class="flex items-center">
                                                        {{-- যদি সাব-টাস্ক থাকে, তাহলে ড্রপডাউন অ্যারো দেখানো হবে --}}
                                                        @if($task->subtasks->isNotEmpty())
                                                            <button @click="open = !open" class="mr-2 p-1 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600 focus:outline-none">
                                                                <svg :class="{'rotate-90': open}" class="w-4 h-4 text-gray-500 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                                                            </button>
                                                        @else
                                                            <div class="w-7 mr-2"></div> {{-- অ্যালাইনমেন্ট ঠিক রাখার জন্য --}}
                                                        @endif
                                                        <span>{{ $task->title }}</span>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <span @class(['px-2 py-1 text-xs font-semibold rounded-full',
                                                        'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' => $task->priority == 'high',
                                                        'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300' => $task->priority == 'medium',
                                                        'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' => $task->priority == 'low',
                                                    ])>{{ ucfirst($task->priority) }}</span>
                                                </td>
                                                <td class="px-4 py-3">
                                                    @php
                                                        $status = $task->is_completed ? 'Completed' : $task->status;
                                                        $statusColor = match($status) {
                                                            'Completed' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                                            'In Progress' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                                                            default => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                                        };
                                                    @endphp
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusColor }}">{{ $status }}</span>
                                                </td>
                                                <td class="px-4 py-3">{{ \Carbon\Carbon::parse($task->due_date)->format('d M, Y, h:i A') }}</td>
                                                <td class="px-4 py-3">
                                                    <div class="flex items-center justify-center gap-2">
                                                        @if(!$task->is_completed)
                                                            <form action="{{ route('tasks.toggleComplete', $task) }}" method="POST"><button type="submit" class="px-2 py-1 text-xs font-medium text-white bg-green-600 rounded-md hover:bg-green-700">Complete</button>@csrf @method('PATCH')</form>
                                                        @endif
                                                        <a href="{{ route('tasks.edit', $task) }}" class="px-3 py-1 text-xs font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700">Edit</a>
                                                        <form action="{{ route('tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('Are you sure?')"><button type="submit" class="px-3 py-1 text-xs font-medium text-white bg-red-600 rounded-md hover:bg-red-700">Delete</button>@csrf @method('DELETE')</form>
                                                    </div>
                                                </td>
                                            </tr>

                                            {{-- সাব-টাস্কের সারিগুলো এখানে দেখানো হবে --}}
                                            @if($task->subtasks->isNotEmpty())
                                                <tr x-show="open" x-collapse class="bg-gray-50 dark:bg-gray-800/50">
                                                    <td colspan="5" class="p-0">
                                                        <div class="px-4 py-2">
                                                            <table class="table-auto w-full">
                                                                <tbody>
                                                                    @foreach($task->subtasks as $subtask)
                                                                        <tr class="border-t border-gray-200 dark:border-gray-700">
                                                                            <td class="pl-12 pr-4 py-2 w-2/5">{{ $subtask->title }}</td>
                                                                            <td class="px-4 py-2">{{-- Subtask Priority --}}</td>
                                                                            <td class="px-4 py-2">...</td> {{-- Subtask Status --}}
                                                                            <td class="px-4 py-2">...</td> {{-- Subtask Due Date --}}
                                                                            <td class="px-4 py-2">...</td> {{-- Subtask Actions --}}
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <p class="text-gray-500 dark:text-gray-400">No tasks found. Try creating a new one!</p>
                        </div>
                    @endif
                    
                    <div class="mt-4">
                        @auth
                            @if($tasks instanceof \Illuminate\Pagination\LengthAwarePaginator)
                                {{ $tasks->links() }}
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
