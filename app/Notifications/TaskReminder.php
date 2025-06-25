<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Task; // Task মডেল ইম্পোর্ট করুন

class TaskReminder extends Notification
{
    use Queueable;

    protected $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $url = route('tasks.show', $this->task);

        return (new MailMessage)
                    ->subject('Task Reminder: ' . $this->task->title)
                    ->greeting('Hi ' . $notifiable->name . ',')
                    ->line('This is a friendly reminder for your upcoming task: "' . $this->task->title . '".')
                    ->line('Due Date: ' . \Carbon\Carbon::parse($this->task->due_date)->format('F d, Y'))
                    ->action('View Task', $url)
                    ->line('Keep up the great work!');
    }
}