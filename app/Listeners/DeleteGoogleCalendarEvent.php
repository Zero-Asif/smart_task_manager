<?php
namespace App\Listeners;
use App\Events\TaskDeleted;
use Google\Client as Google_Client;
use Google\Service\Calendar as Google_Service_Calendar;
use Illuminate\Support\Facades\Log;
class DeleteGoogleCalendarEvent
{
    public function handle(TaskDeleted $event): void
    {
        $task = $event->task;
        $user = $task->user;

        // যদি টাস্কের কোনো गूगल इवेंट আইডি না থাকে, তাহলে কিছুই করার নেই
        if (!$task->google_event_id) {
            return;
        }

        // ব্যবহারকারীর গুগল টোকেন থাকতে হবে
        if (!$user->google_access_token) {
            return;
        }
        
        try {
            $client = new Google_Client();
            $client->setClientId(env('GOOGLE_CLIENT_ID'));
            $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
            // ... (টোকেন হ্যান্ডেল করার সম্পূর্ণ লজিক CreateGoogleCalendarEvent থেকে কপি করে আনা যায়)
            $accessToken = json_decode($user->google_access_token, true);
            $client->setAccessToken($accessToken);

            if ($client->isAccessTokenExpired()) {
                $client->fetchAccessTokenWithRefreshToken($user->google_refresh_token);
                $user->update(['google_access_token' => json_encode($client->getAccessToken())]);
            }

            $service = new Google_Service_Calendar($client);
            $service->events->delete('primary', $task->google_event_id);

            Log::info("Google Calendar event deleted for task #{$task->id}");

        } catch (\Exception $e) {
            Log::error("Failed to delete Google Calendar event for task #{$task->id}. Error: " . $e->getMessage());
        }
    }
}