<x-app-layout>
    <x-slot name="header">
        <div x-data="{ greeting: '' }" x-init="
            const hour = new Date().getHours();
            if (hour < 12) greeting = 'Good Morning';
            else if (hour < 18) greeting = 'Good Afternoon';
            else greeting = 'Good Evening';
        ">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                <span x-text="greeting"></span>, @auth {{ Auth::user()->name }}! @else Guest! @endauth
            </h2>
        </div>
        <!-- <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2> -->
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @auth
                <div class="flex flex-col gap-6">
                    @if(!Auth::user()->hasVerifiedEmail())
                        <div class="mt-6">
                            <x-dashboard.notification-promo-card />
                        </div>
                    @endif

                    {{-- Stat Cards: এখন ৩টি কার্ড থাকবে --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <x-dashboard.stats-card 
                            title="Completed Tasks" 
                            :count="$completedTasks" 
                            :tasks="$completedTasksCollection" 
                            icon='<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>' 
                        />
                        <x-dashboard.stats-card 
                            title="Pending Tasks" 
                            :count="$pendingTasks + $inProgressTasks" {{-- Pending এবং In Progress একসাথে দেখানো হচ্ছে --}}
                            :tasks="$pendingTasksCollection->merge($inProgressTasksCollection)" 
                            icon='<path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>' 
                        />
                        <x-dashboard.stats-card 
                            title="Overdue Tasks" 
                            :count="$overdueTasks" 
                            :tasks="$overdueTasksCollection" 
                            icon='<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>' 
                        />
                    </div>

                    {{-- Main Dashboard Area --}}
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div class="lg:col-span-2 flex flex-col gap-6">
                            <x-dashboard.weekly-chart-widget :labels="$weeklyChartLabels" :data="$weeklyChartData" />
                            <x-dashboard.quick-add-widget />
                        </div>
                        <div class="lg:col-span-1 flex flex-col gap-6">
                            <x-dashboard.task-overview-chart :completed="$completedTasks" :pending="$pendingTasks" :inProgress="$inProgressTasks" />
                            {{-- নিচের Overdue উইজেটটি ডিলেট করা হয়েছে --}}
                            <x-dashboard.task-list-widget :tasks="$upcomingTasks" title="Upcoming Tasks" />
                            <x-dashboard.activity-log-widget :activities="$activities" />
                        </div>
                    </div>
                </div>
            @endauth
        </div>
    </div>
</x-app-layout>
