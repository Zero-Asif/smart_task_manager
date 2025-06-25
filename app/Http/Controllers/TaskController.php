<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Events\TaskCreated; 
use App\Events\TaskDeleted; 
use App\Events\TaskUpdated;



class TaskController extends Controller
{
    private function getSessionTasks()
    {
        return collect(session('tasks', []));
    }

    public function index(Request $request)
    {
        if (Auth::check()) {
            $query = Auth::user()->tasks();
            // ... আপনার ফিল্টারিং এবং সর্টিং লজিক ...
            $tasks = $query->latest()->paginate(10);
        } else {
            $tasks = $this->getSessionTasks();
        }
        return view('tasks.index', compact('tasks'));
    }

    public function create()
    {
        return view('tasks.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'required|date_format:Y-m-d\TH:i',
            'priority' => 'required|in:Low,Medium,High',
        ]);
        $validatedData['is_completed'] = false;

        if (Auth::check()) {
            $user = Auth::user();
            $task = $user->tasks()->create($validatedData);

            // এখন এটি সঠিকভাবে কাজ করবে
            TaskCreated::dispatch($task);

        } else {
            $tasks = $this->getSessionTasks();
            $validatedData['id'] = Str::uuid()->toString();
            $tasks->push($validatedData);
            session(['tasks' => $tasks->all()]);
        }

        return redirect()->route('tasks.index')->with('success', 'Task created successfully!');
    }
    
    // edit, update, destroy ইত্যাদি মেথডগুলো শুধুমাত্র লগইন করা ব্যবহারকারীদের জন্য
    public function show(Task $task)
    {
        if (Auth::guest() || $task->user_id !== Auth::id()) {
            return redirect()->route('login')->with('error', 'You must be logged in to view this page.');
        }
        return view('tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        if (Auth::guest() || $task->user_id !== Auth::id()) {
            return redirect()->route('login')->with('error', 'You must be logged in to edit tasks.');
        }
        return view('tasks.edit', compact('task'));
    }

    public function update(Request $request, Task $task)
    {
        if (Auth::guest() || $task->user_id !== Auth::id()) {
            abort(403);
        }
        $task->update($request->all());

        // আপডেট হওয়ার পর ইভেন্ট ফায়ার করা হচ্ছে
        TaskUpdated::dispatch($task);

        return redirect()->route('tasks.index')->with('success', 'Task updated successfully.');
    }

    public function destroy(Task $task)
    {
        if (Auth::guest() || $task->user_id !== Auth::id()) {
            abort(403);
        }
        // এখানে ক্যালেন্ডার ইভেন্ট ডিলিটের লজিক থাকবে
        TaskDeleted::dispatch($task);

        $task->delete();
        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully.');
    }

    public function toggleComplete(Task $task)
    {
        if (Auth::guest() || $task->user_id !== Auth::id()) {
            abort(403);
        }
        $task->is_completed = !$task->is_completed;
        $task->save();
        return back()->with('success', 'Task status updated!');
    }


    public function startTask(Request $request, Task $task)
    {
        // URL Signature ভ্যালিডেশন (যদি signed রুট ব্যবহার করেন)
        if (! $request->hasValidSignature()) {
            abort(401, 'Unauthorized');
        }

        if (!Auth::check() || Auth::id() !== $task->user_id) {
            Auth::login($task->user);
        }

            $task->status = 'In Progress';
            $task->save();

            // নতুন তৈরি করা ভিউ ফাইলটি রিটার্ন করা হচ্ছে
            return view('tasks.task-started');
    }
        

    
    public function markAsCompleteFromEmail(Request $request, Task $task)
    {
        // নিশ্চিত করা হচ্ছে যে ইউআরএলটি টেম্পার করা হয়নি
        if (! $request->hasValidSignature()) {
            abort(401);
        }

        $task->is_completed = true;
        $task->status = 'Completed'; // স্ট্যাটাসও আপডেট করা হচ্ছে
        $task->save();

        return '<h1>Thank you! Your task has been marked as complete.</h1>';
    }

    public function quickStore(Request $request)
    {
        if (Auth::guest()) {
            return response()->json(['success' => false, 'message' => 'Please log in to use this feature.'], 401);
        }
        $request->validate(['title' => 'required|string|max:255']);
        $user = auth()->user();
        $task = $user->tasks()->create([
            'title' => $request->title,
            'due_date' => now()->addDay(),
            'priority' => 'Medium',
        ]);
        return response()->json(['success' => true, 'message' => 'Task added successfully!']);
    }
}