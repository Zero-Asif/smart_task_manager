<?php

namespace App\Events;

use App\Models\Task; // পরিবর্তন ১: Task মডেল ইম্পোর্ট করা হয়েছে
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The task instance.
     *
     * @var \App\Models\Task
     */
    public Task $task; // পরিবর্তন ২: একটি পাবলিক প্রোপার্টি যোগ করা হয়েছে

    /**
     * Create a new event instance.
     */
    public function __construct(Task $task) // পরিবর্তন ৩: কনস্ট্রাক্টরে Task অবজেক্ট গ্রহণ করা হয়েছে
    {
        $this->task = $task; // এবং সেটিকে পাবলিক প্রোপার্টিতে সেট করা হয়েছে
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}