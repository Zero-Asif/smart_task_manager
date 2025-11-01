<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Events\TaskCreated;
use App\Events\TaskDeleted;
use App\Events\TaskUpdated;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
// পরিবর্তন ১: আগের use স্টেটমেন্ট মুছে OpenAI যোগ করা হয়েছে
use OpenAI; 

class TaskController extends Controller
{
    private function getSessionTasks()
    {
        return collect(session('tasks', []));
    }

    public function index(Request $request)
    {
        $tasks = collect([]);

        if (Auth::check()) {
            // পরিবর্তন: শুধুমাত্র প্যারেন্ট টাস্কগুলো দেখানোর জন্য whereNull('parent_id') যোগ করা হয়েছে
            $query = Auth::user()->tasks()->with('subtasks')->whereNull('parent_id');
            
            // ...ভবিষ্যৎ ফিল্টারিং এবং সর্টিং লজিক এখানে থাকতে পারে ...
            
            $tasks = $query->latest()->paginate(10);
        } else {
            // গেস্ট ব্যবহারকারীদের জন্য সেশন-ভিত্তিক টাস্ক
            $sessionTasks = collect(session('tasks', []))->whereNull('parent_id');

            $tasks = $sessionTasks->map(function ($taskArray) {
                $taskObject = (object)$taskArray;

                $taskObject->subtasks = collect(session('tasks', []))
                    ->where('parent_id', $taskObject->id)
                    ->map(function ($subtaskArray) {
                        return (object)$subtaskArray;
                    });
                return $taskObject;
            });

        }
        return view('tasks.index', compact('tasks'));
    }

    public function create()
    {
        return view('tasks.create');
    }
    
    public function store(Request $request)
    {
        // ১. ইনপুট ডেটা ভ্যালিডেট করা
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'required|date_format:Y-m-d\TH:i',
            'priority' => 'required|in:low,medium,high',
            'subtasks' => 'sometimes|array',
            'subtasks.*' => 'nullable|string|max:255',
        ]);

        // ২. ব্যবহারকারী লগইন করা আছে কিনা তা পরীক্ষা করা
        if (Auth::check()) {
            $user = Auth::user();

            // ৩. মূল টাস্কটি শুধু একবার তৈরি করা (ডুপ্লিকেট তৈরির সমস্যা সমাধান করা হয়েছে)
            $mainTask = $user->tasks()->create([
                'title' => $validatedData['title'],
                'description' => $validatedData['description'],
                'due_date' => $validatedData['due_date'],
                'priority' => $validatedData['priority'],
                'is_completed' => false,
                'status' => 'Pending',
            ]);

            TaskCreated::dispatch($mainTask);

            // ৪. যদি সাব-টাস্ক থাকে, তাহলে সেগুলো তৈরি এবং সেভ করা
            if (!empty($validatedData['subtasks'])) {
                foreach ($validatedData['subtasks'] as $subtaskTitle) {
                    if (empty(trim($subtaskTitle))) continue;
                    $user->tasks()->create([
                        'title' => $subtaskTitle,
                        'due_date' => $validatedData['due_date'],
                        'priority' => $validatedData['priority'],
                        'parent_id' => $mainTask->id,
                        'is_completed' => false,
                        'status' => 'Pending',
                    ]);
                }
            }
        } else {
            // ৫. যদি ব্যবহারকারী লগইন করা না থাকে, তাহলে সেশনে সেভ হবে
            $tasks = $this->getSessionTasks()->all();

            // মূল টাস্কের ডেটা তৈরি করা
            $mainTaskData = [
                'id' => \Illuminate\Support\Str::uuid()->toString(),
                'title' => $validatedData['title'],
                'description' => $validatedData['description'],
                'due_date' => $validatedData['due_date'],
                'priority' => $validatedData['priority'],
                'is_completed' => false,
                'status' => 'Pending',
                'parent_id' => null, // এটি একটি প্যারেন্ট টাস্ক
            ];
            $tasks[] = $mainTaskData;

            // যদি সাব-টাস্ক থাকে, সেগুলোও সেশনে যোগ করা
            if (!empty($validatedData['subtasks'])) {
                foreach ($validatedData['subtasks'] as $subtaskTitle) {
                    if (empty(trim($subtaskTitle))) continue;

                    $tasks[] = [
                        'id' => \Illuminate\Support\Str::uuid()->toString(),
                        'parent_id' => $mainTaskData['id'], // প্যারেন্ট টাস্কের আইডির সাথে লিঙ্ক করা
                        'title' => $subtaskTitle,
                        'due_date' => $validatedData['due_date'],
                        'priority' => $validatedData['priority'],
                        'is_completed' => false,
                        'status' => 'Pending',
                    ];
                }
            }
            session(['tasks' => $tasks]);
        }

        // ৬. সবশেষে ইনডেক্স পেজে রিডাইরেক্ট করা
        return redirect()->route('tasks.index')->with('success', 'Task created successfully!');
    }
    
    public function show(Task $task)
    {
        if (Auth::guest() || $task->user_id !== Auth::id()) {
            return redirect()->route('login')->with('error', 'You must be logged in to view this page.');
        }
        // সাব-টাস্কগুলো লোড করার জন্য
        $task->load('subtasks');
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

        TaskUpdated::dispatch($task);

        return redirect()->route('tasks.index')->with('success', 'Task updated successfully.');
    }

    public function destroy(Task $task)
    {
        if (Auth::guest() || $task->user_id !== Auth::id()) {
            abort(403);
        }
        
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
        $task->status = $task->is_completed ? 'Completed' : 'Pending';
        $task->save();
        return back()->with('success', 'Task status updated!');
    }

    public function quickStore(Request $request)
    {
        if (Auth::guest()) {
            return response()->json(['success' => false, 'message' => 'Please log in to use this feature.'], 401);
        }
        $request->validate(['title' => 'required|string|max:255']);
        $user = Auth::user();
        $task = $user->tasks()->create([
            'title' => $request->title,
            'due_date' => now()->addDay(),
            'priority' => 'medium', // ছোট হাতের অক্ষর ব্যবহার করা হয়েছে
        ]);
        return response()->json(['success' => true, 'message' => 'Task added successfully!']);
    }

    public function startTask(Request $request, Task $task)
    {
        if (! $request->hasValidSignature()) {
            abort(401, 'Unauthorized');
        }

        if (!Auth::check() || Auth::id() !== $task->user_id) {
            Auth::login($task->user);
        }

        $task->status = 'In Progress';
        $task->save();

        return view('tasks.task-started');
    }

    public function generateSubtasks(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // LM Studio-এর লোকাল সার্ভারের ঠিকানা
        $localApiUrl = 'http://localhost:1234/v1/chat/completions';

        // --- এই নতুন এবং ডায়নামিক Prompt টি ব্যবহার করুন ---

        $prompt = "You are a world-class expert planner and personal life coach. Your task is to analyze a user's goal and create a detailed, actionable, and logical plan with specific steps.

        First, analyze the user's input to understand the category of the task (e.g., travel, career preparation, education/exam, event planning, health & fitness, etc.).

        Second, adopt the persona of an expert in that specific field. For example:
        - If the task is about travel, act as an expert tour guide.
        - If the task is about a job interview, act as an expert career coach.
        - If the task is about an exam, act as an expert academic advisor.

        Finally, generate a detailed plan to achieve the user's goal.

        You will be given a task title, a description, and a primary date/deadline.
        - Task Title: \"{$request->title}\"
        - Description: \"{$request->description}\"
        - Event Date/Deadline: \"{$request->due_date}\"

        Based on this information, generate a step-by-step plan.
        Your response MUST BE ONLY a valid JSON array of strings.
        Each string in the array should be a specific sub-task. For tasks with a schedule, use the format: \"[DD-MM-YYYY HH:MM AM/PM] - [Activity Description]\"

        The plan should be practical and include all necessary details. For example, for an interview, include logistical steps like travel time, what to prepare, when to sleep, and when to wake up. For a trip, include specific places to visit, meal times, etc.

        Do not include any other text, explanations, or markdown formatting like ```json. The output should be a pure JSON array.";

        try {
            // LM Studio OpenAI-এর মতো API ফরম্যাট ব্যবহার করে
            $response = Http::timeout(120) // লোকাল মডেল চলতে সময় লাগতে পারে
                ->post($localApiUrl, [
                    'model' => 'vikhr-gemma-2b-instruct',
                    'messages' => [
                        ['role' => 'system', 'content' => 'You always respond in valid JSON.'],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'temperature' => 0.7,
                ]);

            if ($response->failed()) {
                Log::error('Local AI Server Error: ' . $response->body());
                return response()->json(['error' => 'Failed to communicate with the local AI server. Is LM Studio server running?'], 500);
            }

            $result = $response->json();
            $responseText = $result['choices'][0]['message']['content'] ?? '';

            $jsonResponse = '';
            // রেগুলার এক্সপ্রেশন ব্যবহার করে টেক্সটের ভেতর থেকে শুধুমাত্র JSON অংশটি বের করে আনা হচ্ছে
            if (preg_match('/(\[.*\]|\{.*\})/s', $responseText, $matches)) {
                $jsonResponse = $matches[0];
            }

            $subtasks = json_decode($jsonResponse, true);

            if (json_last_error() !== JSON_ERROR_NONE || !is_array($subtasks)) {
                Log::error('Local AI returned an invalid JSON structure: ' . $responseText);
                return response()->json(['error' => 'The local AI returned an unexpected format.'], 500);
            }

            return response()->json($subtasks);

        } catch (\Exception $e) {
            Log::error('Local AI Request Exception: ' . $e->getMessage());
            return response()->json(['error' => 'A critical error occurred while communicating with the local AI.'], 500);
        }
    }

    public function markAsCompleteFromEmail(Request $request, Task $task)
    {
        if (! $request->hasValidSignature()) {
            abort(401);
        }

        $task->is_completed = true;
        $task->status = 'Completed';
        $task->save();

        return '<h1>Thank you! Your task has been marked as complete.</h1>';
    }

    // মুছে ফেলা হয়েছে: debugAi() মেথডটি এখন আর প্রয়োজন নেই, তাই এটি মুছে দিয়ে কোড পরিষ্কার রাখা হয়েছে।
}