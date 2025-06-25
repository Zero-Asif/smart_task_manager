<?php
namespace App\Console\Commands;
use App\Models\User;
use App\Notifications\OverdueTasksDigest;
use Illuminate\Console\Command;
class SendOverdueTaskDigest extends Command
{
    protected $signature = 'app:send-overdue-task-digest';
    protected $description = 'Send a digest of overdue tasks to users.';
    public function handle()
    {
        $users = User::whereHas('tasks', function ($query) {
            $query->where('is_completed', false)->where('due_date', '<', now());
        })->get();

        foreach ($users as $user) {
            $overdueTasks = $user->tasks()
                ->where('is_completed', false)
                ->where('due_date', '<', now())
                ->get();

            if ($overdueTasks->isNotEmpty()) {
                $user->notify(new OverdueTasksDigest($overdueTasks));
            }
        }
        $this->info('Overdue task digests have been sent successfully!');
    }
}