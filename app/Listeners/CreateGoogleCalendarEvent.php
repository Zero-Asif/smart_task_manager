<?php

namespace App\Listeners;

use App\Events\TaskCreated;
use Carbon\Carbon;
use Google\Client as Google_Client;
use Google\Service\Calendar as Google_Service_Calendar;
use Google\Service\Calendar\Event as Google_Service_Calendar_Event;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class CreateGoogleCalendarEvent implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(TaskCreated $event): void
    {
        // ... user, task, token checks ...
        $task = $event->task;
        $user = $task->user;

        if (!($user->preferences['calendar_event'] ?? false) || !$user->google_access_token) {
            return;
        }

        try {
            // ... client setup and token refresh logic ...
             $client = new Google_Client();
            $client->setClientId(env('GOOGLE_CLIENT_ID'));
            $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
            $accessToken = json_decode($user->google_access_token, true);
            $client->setAccessToken($accessToken);
            if ($client->isAccessTokenExpired()) {
                $client->fetchAccessTokenWithRefreshToken($user->google_refresh_token);
                $user->update(['google_access_token' => json_encode($client->getAccessToken())]);
            }

            // --- Priority অনুযায়ী একাধিক রিমাইন্ডার সেট করার নতুন লজিক ---
            $reminderOverrides = [];
            switch (strtolower($task->priority)) {
                case 'high':
                    $reminderOverrides = [
                        ['method' => 'popup', 'minutes' => 15],
                        ['method' => 'popup', 'minutes' => 10],
                    ];
                    break;
                case 'medium':
                    $reminderOverrides = [
                        ['method' => 'popup', 'minutes' => 10],
                    ];
                    break;
                case 'low':
                    $reminderOverrides = [
                        ['method' => 'popup', 'minutes' => 5],
                    ];
                    break;
            }

            $reminders = [
                'useDefault' => false,
                'overrides' => $reminderOverrides,
            ];
            // --- শেষ ---
            
            $startLink = URL::signedRoute('tasks.start', ['task' => $task->id]);
            $description = $task->description . "\n\n" . "Click here to mark the task as 'In Progress': " . $startLink;

            $calendarEvent = new Google_Service_Calendar_Event([
                'summary' => $task->title,
                'description' => $description,
                'start' => [
                    'dateTime' => Carbon::parse($task->due_date, config('app.timezone'))->toRfc3339String(),
                    'timeZone' => config('app.timezone'),
                ],
                'end' => [
                    'dateTime' => Carbon::parse($task->due_date, config('app.timezone'))->addMinutes(30)->toRfc3339String(),
                    'timeZone' => config('app.timezone'),
                ],
                'reminders' => $reminders, // <-- রিমাইন্ডার অ্যারেটি এখানে যোগ করা হয়েছে
            ]);
            
            $service = new Google_Service_Calendar($client);
            $createdEvent = $service->events->insert('primary', $calendarEvent);
            $task->update(['google_event_id' => $createdEvent->getId()]);
            Log::info("Google Calendar event created for task #{$task->id}");

        } catch (\Exception $e) {
            Log::error("Failed to create Google Calendar event for task #{$task->id}. Error: " . $e->getMessage());
        }
    }
}

