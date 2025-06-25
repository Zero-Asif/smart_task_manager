<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;
use App\Notifications\TaskReminder;
use App\Notifications\TaskReminderSms;
use Illuminate\Support\Carbon;
// use Carbon\Carbon;

class SendTaskReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-task-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    // protected $description = 'Sends reminders for tasks that are due on the next day.';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Find tasks that are due soon and send reminders to users.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Sending task reminders...');

        $reminderTimeStart = Carbon::now()->addDay()->startOfMinute();
        $reminderTimeEnd = $reminderTimeStart->copy()->endOfMinute();

        // যে টাস্কগুলো সম্পন্ন হয়নি এবং সেগুলোর ডিউ ডেট আগামীকাল, সেগুলো খুঁজুন
        $tasks = Task::where('is_completed', false)
                     ->whereBetween('due_date', [$reminderTimeStart, $reminderTimeEnd])
                     ->whereDate('due_date', now()->addDay()->toDateString())
                     ->get();
        
        if ($tasks->isEmpty()) {
            $this->info('No tasks found for reminders at this time.');
            return Command::SUCCESS;
        }

        $this->info("Found {$tasks->count()} tasks due tomorrow. Preparing to send reminders...");

        foreach ($tasks as $task) {
            $user = $task->user;
            
            if ($user) {
                // ব্যবহারকারীর নোটিফিকেশন পছন্দগুলো নিন
                $preferences = $user->preferences ?? [];

                // ব্যবহারকারী ইমেইল রিমাইন্ডার চাইলে এবং তার ইমেইল ভেরিফাইড হলে ইমেইল পাঠান
                if (isset($preferences['task_reminders_email']) && $preferences['task_reminders_email']) {
                    $user->notify(new TaskReminder($task));
                    $this->info("Email reminder sent for task #{$task->id} to {$user->email}");
                }

                // যেহেতু ফোন নম্বর ফিচারটি বাদ দেওয়া হয়েছে, তাই SMS পাঠানোর লজিকটি এখানে নেই
            }
        }
        
        $this->info("Done. Sent reminders for {$tasks->count()} tasks.");
        return Command::SUCCESS;
    }
}