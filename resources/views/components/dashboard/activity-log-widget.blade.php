@props(['activities'])

<div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg h-full">
    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-4">Recent Activity</h3>
    @if($activities->count() > 0)
        <ul class="space-y-4">
            @foreach($activities as $activity)
                <li class="flex items-start gap-3">
                    <div class="flex-shrink-0">
                        <span class="inline-flex items-center justify-center h-8 w-8 rounded-full
                            @switch($activity->description)
                                @case('Task has been created') bg-green-100 dark:bg-green-900 @break
                                @case('Task has been updated') bg-yellow-100 dark:bg-yellow-900 @break
                                @case('Task has been deleted') bg-red-100 dark:bg-red-900 @break
                                @default bg-gray-100 dark:bg-gray-700
                            @endswitch
                        ">
                            @switch($activity->description)
                                @case('Task has been created')
                                    <svg class="h-5 w-5 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                                    @break
                                @case('Task has been updated')
                                    <svg class="h-5 w-5 text-yellow-600 dark:text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                    @break
                                @case('Task has been deleted')
                                     <svg class="h-5 w-5 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    @break
                            @endswitch
                        </span>
                    </div>
                    <div class="flex-grow text-sm">
                        <p class="text-gray-800 dark:text-gray-200">
                            You {{ $activity->description === 'Task has been updated' ? 'updated the task' : ($activity->description === 'Task has been created' ? 'created the task' : 'deleted the task') }}
                            <span class="font-bold">{{ $activity->properties['attributes']['title'] ?? ($activity->properties['old']['title'] ?? 'N/A') }}</span>
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            {{ $activity->created_at->diffForHumans() }}
                        </p>
                        {{-- Undo Button for Deleted Tasks --}}
                        @if($activity->description === 'Task has been deleted')
                            <form action="{{ route('activities.undo', $activity) }}" method="POST" class="inline-block mt-1">
                                @csrf
                                <button type="submit" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">Undo</button>
                            </form>
                        @endif
                    </div>
                </li>
            @endforeach
        </ul>
    @else
        <p class="text-center text-sm text-gray-500 dark:text-gray-400 py-8">No recent activity in the last 30 days.</p>
    @endif
</div>