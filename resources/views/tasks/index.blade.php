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

                    <div class="flex items-center justify-between mb-6">
                        {{-- Create Task Button --}}
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
                            <table class="table-auto w-full border-collapse border border-gray-200 dark:border-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="border px-4 py-2 text-left">Title</th>
                                        <th class="border px-4 py-2 text-left">Priority</th>
                                        <th class="border px-4 py-2 text-left">Status</th>
                                        <th class="border px-4 py-2 text-left">Due Date</th>
                                        <th class="border px-4 py-2 text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tasks as $task)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-600">
                                            <td class="border px-4 py-2">{{ data_get($task, 'title') }}</td>
                                            <td class="border px-4 py-2">
                                                <span @class([
                                                    'px-2 py-1 text-xs font-semibold rounded-full',
                                                    'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' => data_get($task, 'priority') == 'high',
                                                    'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300' => data_get($task, 'priority') == 'medium',
                                                    'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' => data_get($task, 'priority') == 'low',
                                                ])>
                                                    {{ ucfirst(data_get($task, 'priority')) }}
                                                </span>
                                            </td>
                                            <td class="border px-4 py-2">
                                                {{-- Status Badge with new 'In Progress' state --}}
                                                @php
                                                    $status = data_get($task, 'is_completed') ? 'Completed' : data_get($task, 'status', 'Pending');
                                                    $statusColor = match($status) {
                                                        'Completed' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                                        'In Progress' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                                                        default => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                                    };
                                                @endphp
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusColor }}">
                                                    {{ $status }}
                                                </span>
                                            </td>
                                            <td class="border px-4 py-2">
                                                {{ \Carbon\Carbon::parse(data_get($task, 'due_date'))->format('d M, Y, h:i A') }}
                                            </td>
                                            <td class="border px-4 py-2">
                                                <div class="flex items-center justify-center gap-2">
                                                    @auth
                                                        @if(!$task->is_completed)
                                                            <form action="{{ route('tasks.toggleComplete', $task) }}" method="POST">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit" class="px-2 py-1 text-xs font-medium text-white bg-green-600 rounded-md hover:bg-green-700">Complete</button>
                                                            </form>
                                                        @endif
                                                        <a href="{{ route('tasks.edit', $task) }}" class="px-3 py-1 text-xs font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700">Edit</a>
                                                        <form action="{{ route('tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="px-3 py-1 text-xs font-medium text-white bg-red-600 rounded-md hover:bg-red-700">Delete</button>
                                                        </form>
                                                    @endauth
                                                </div>
                                            </td>
                                        </tr>
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
