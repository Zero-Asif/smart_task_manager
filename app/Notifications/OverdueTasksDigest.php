<?php
namespace App\Notifications;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\URL;

class OverdueTasksDigest extends Notification
{
    use Queueable;
    public Collection $overdueTasks;

    public function __construct(Collection $overdueTasks)
    {
        $this->overdueTasks = $overdueTasks;
    }

    public function via(object $notifiable): array { return ['mail']; }

    public function toMail(object $notifiable): MailMessage
    {
        $mailMessage = (new MailMessage)
            ->subject('You Have Overdue Tasks!')
            ->greeting('Hi ' . $notifiable->name . ',')
            ->line('This is a friendly reminder that you have some tasks that are past their due date. Did you complete them?');

        foreach ($this->overdueTasks as $task) {
            // প্রতিটি টাস্কের জন্য একটি নিরাপদ, স্বাক্ষরযুক্ত ইউআরএল তৈরি করা হচ্ছে
            $completeUrl = URL::temporarySignedRoute(
                'tasks.complete.email', now()->addDays(7), ['task' => $task->id]
            );

            $mailMessage->line("• {$task->title} (Due: {$task->due_date->format('d M, Y')})")
                        ->action('Mark As Complete', $completeUrl);
        }

        $mailMessage->line('You can view all your tasks on your dashboard.');
        return $mailMessage;
    }
}