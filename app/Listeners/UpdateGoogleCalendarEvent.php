<?php

namespace App\Listeners;

use App\Events\TaskUpdated;
use Carbon\Carbon;
use Google\Client as Google_Client;
use Google\Service\Calendar as Google_Service_Calendar;
use Google\Service\Calendar\EventDateTime as Google_Service_Calendar_EventDateTime;
use Google\Service\Calendar\EventReminders as Google_Service_Calendar_EventReminders;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class UpdateGoogleCalendarEvent implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(TaskUpdated $event): void
    {
        $task = $event->task;
        $user = $task->user;

        if (!$task->google_event_id || !$user->google_access_token) {
            // Silently return if no event ID or token exists
            return;
        }
        
        try {
            // Setup Google Client and refresh token if needed
            $client = new Google_Client();
            $client->setClientId(env('GOOGLE_CLIENT_ID'));
            $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
            
            $accessToken = json_decode($user->google_access_token, true);
            $client->setAccessToken($accessToken);

            if ($client->isAccessTokenExpired()) {
                $client->fetchAccessTokenWithRefreshToken($user->google_refresh_token);
                $user->update(['google_access_token' => json_encode($client->getAccessToken())]);
            }

            $service = new Google_Service_Calendar($client);
            $calendarId = 'primary';
            $googleEvent = $service->events->get($calendarId, $task->google_event_id);

            // Update event summary and description with the start link
            $googleEvent->setSummary($task->title);
            $startLink = URL::signedRoute('tasks.start', ['task' => $task->id]);
            $description = $task->description . "\n\n" . "Click here to mark the task as 'In Progress': " . $startLink;
            $googleEvent->setDescription($description);

            // Update event start and end times
            $start = new Google_Service_Calendar_EventDateTime();
            $start->setDateTime(Carbon::parse($task->due_date, config('app.timezone'))->toRfc3339String());
            $googleEvent->setStart($start);

            $end = new Google_Service_Calendar_EventDateTime();
            $end->setDateTime(Carbon::parse($task->due_date, config('app.timezone'))->addMinutes(30)->toRfc3339String());
            $googleEvent->setEnd($end);

            // --- Logic for setting multiple reminders based on priority ---
            $reminderOverrides = [];
            switch (strtolower($task->priority)) {
                case 'high':
                    $reminderOverrides = [
                        ['method' => 'popup', 'minutes' => 15],
                        ['method' => 'popup', 'minutes' => 10], // Second reminder
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

            $reminders = new Google_Service_Calendar_EventReminders();
            $reminders->setUseDefault(false);
            if (!empty($reminderOverrides)) {
                $reminders->setOverrides($reminderOverrides);
            }
            $googleEvent->setReminders($reminders);
            // --- End of reminder logic ---

            // Save the updated event
            $service->events->update($calendarId, $googleEvent->getId(), $googleEvent);

            Log::info("Google Calendar event updated for task #{$task->id}");

        } catch (\Exception $e) {
            Log::error("Failed to update Google Calendar event for task #{$task->id}. Error: " . $e->getMessage());
        }
    }
}
