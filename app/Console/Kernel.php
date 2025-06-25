<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // প্রতিদিন সকালে ৮টায় এই কমান্ডটি চালানোর জন্য শিডিউল করা হলো
        $schedule->command('app:send-task-reminders')->everyMinute();
        $schedule->command('app:send-overdue-task-digest')->dailyAt('08:00');
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}