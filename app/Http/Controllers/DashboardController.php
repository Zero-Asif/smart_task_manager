<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // ডিফল্ট খালি মান সেট করা হচ্ছে
        $viewData = [
            'completedTasks' => 0,
            'pendingTasks' => 0,
            'overdueTasks' => 0,
            'inProgressTasks' => 0,
            'completedTasksCollection' => collect(),
            'pendingTasksCollection' => collect(),
            'overdueTasksCollection' => collect(),
            'inProgressTasksCollection' => collect(),
            'upcomingTasks' => collect(),
            'weeklyChartData' => json_encode(array_fill(0, 7, 0)),
            'weeklyChartLabels' => json_encode(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']),
            'activities' => collect(),
        ];

        // যদি ব্যবহারকারী লগইন করা থাকে, তাহলে ডাটাবেস থেকে আসল ডেটা আনুন
        if (Auth::check()) {
            $user = Auth::user();

            
            $completedTasksCollection = $user->tasks()->where('is_completed', true)->latest('updated_at')->get();
            $nonCompletedTasks = $user->tasks()->where('is_completed', false)->get();

            $pendingTasksCollection = $nonCompletedTasks->where('status', 'Pending');
            $inProgressTasksCollection = $nonCompletedTasks->where('status', 'In Progress');
            $overdueTasksCollection = $nonCompletedTasks->where('due_date', '<', now());
            // $upcomingTasks = $nonCompletedTasks->where('due_date', '>=', now())->orderBy('due_date', 'asc')->limit(5)->get();
            $upcomingTasks = $user->tasks()
                                 ->where('is_completed', false)
                                 ->where('due_date', '>=', now())
                                 ->orderBy('due_date', 'asc')
                                 ->limit(5)
                                 ->get();

            $pendingTasksCollection = $user->tasks()->where('is_completed', false)->get();
            $overdueTasksCollection = $pendingTasksCollection->where('due_date', '<', now());
            $upcomingTasks = $user->tasks()->where('is_completed', false)->where('due_date', '>=', now())->orderBy('due_date', 'asc')->limit(5)->get();

            $viewData['completedTasks'] = $completedTasksCollection->count();
            $viewData['pendingTasks'] = $pendingTasksCollection->count();
            $viewData['overdueTasks'] = $overdueTasksCollection->count();
            $viewData['inProgressTasks'] = $inProgressTasksCollection->count();

            $viewData['completedTasksCollection'] = $completedTasksCollection;
            $viewData['pendingTasksCollection'] = $pendingTasksCollection;
            $viewData['inProgressTasksCollection'] = $inProgressTasksCollection;
            $viewData['overdueTasksCollection'] = $user->tasks()->where('is_completed', false)->where('due_date', '<', now())->latest('due_date')->get();
            $viewData['upcomingTasks'] = $upcomingTasks;
            
            $weeklyData = [];
            $labels = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::today()->subDays($i);
                $labels[] = $date->format('D');
                $weeklyData[] = $user->tasks()->where('is_completed', true)->whereDate('updated_at', $date)->count();
            }
            $viewData['weeklyChartLabels'] = json_encode($labels);
            $viewData['weeklyChartData'] = json_encode($weeklyData);
            
            $viewData['activities'] = Activity::where('causer_id', $user->id)->latest()->limit(10)->get();
        }

        return view('dashboard', $viewData);
    }
}