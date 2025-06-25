<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class GoogleCalendarController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirect()
    {
        return Socialite::driver('google')
            ->scopes(['https://www.googleapis.com/auth/calendar.events'])
            ->with(["access_type" => "offline", "prompt" => "consent"])
            ->redirect();
    }

    /**
     * Obtain the user information from Google.
     */
    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
            $user = Auth::user();

            // ডিবাগিং: গুগল থেকে কি কি তথ্য আসছে তা পরীক্ষা করা
            // dd($googleUser); // <-- প্রথমবার এই লাইনটি আনকমেন্ট করে পরীক্ষা করতে পারেন

            // টোকেন সেভ করার জন্য একটি অ্যারে তৈরি করা
            $updateData = [
                'google_id' => $googleUser->getId(),
                // Access Token একটি অবজেক্ট হতে পারে, তাই এটিকে JSON হিসেবে সেভ করা নিরাপদ
                'google_access_token' => json_encode($googleUser->token),
            ];

            // গুগল শুধুমাত্র প্রথমবারই Refresh Token পাঠায়। তাই এটি null না হলেই শুধু আপডেট করব।
            if ($googleUser->refreshToken) {
                $updateData['google_refresh_token'] = $googleUser->refreshToken;
            }

            $user->update($updateData);
            
            Log::info("User {$user->id} successfully connected their Google Account.");

            return redirect()->route('profile.edit')->with('status', 'google-calendar-connected');

        } catch (\Exception $e) {
            Log::error('Google Calendar Callback Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('profile.edit')->with('error', 'Failed to connect to Google Calendar. Please try again.');
        }
    }
}