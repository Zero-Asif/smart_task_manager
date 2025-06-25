<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use App\Models\Task; // Task মডেল ইম্পোর্ট করুন
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Events\TaskCreated;
use App\Events\TaskDeleted;
use App\Events\TaskUpdated;

class ActivityController extends Controller
{
    public function undo(Activity $activity)
    {
        // নিশ্চিত করুন যে ব্যবহারকারী নিজের অ্যাক্টিভিটিই undo করছে
        if ($activity->causer_id !== auth()->id()) {
            abort(403);
        }

        $subjectModel = $activity->subject_type; // যেমন: App\Models\Task
        if (!class_exists($subjectModel)) {
             return back()->with('error', 'Cannot restore. Model not found.');
        }

        $restoredTask = $subjectModel::onlyTrashed()->find($activity->subject_id);

        if ($restoredTask) {
            $restoredTask->restore();

            // এখানে মূল সমাধান: টাস্ক restore হওয়ার পর TaskCreated ইভেন্ট ফায়ার করা হচ্ছে
            // যাতে ক্যালেন্ডার ইভেন্ট আবার তৈরি হয়।
            TaskCreated::dispatch($restoredTask);

            return back()->with('success', 'The task has been restored successfully.');
        }

        // শুধুমাত্র ডিলিট করা টাস্কই undo করা যাবে
        if ($activity->description === 'Task has been deleted' && $activity->subject_type === Task::class) {
            // subject_id ব্যবহার করে soft-deleted টাস্কটি খুঁজুন
            $task = Task::withTrashed()->find($activity->subject_id);

            if ($task) {
                $task->restore(); // টাস্কটি restore করুন

                // ঐচ্ছিক: সফলভাবে restore হওয়ার পর 'deleted' লগটি ডিলিট করে দিন
                $activity->delete();

                return back()->with('success', 'The task has been restored successfully!');
            }
        }

        return back()->with('error', 'Unable to undo this action.');
    }
}