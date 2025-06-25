<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    // +++ নতুন এবং সঠিক কোড +++

    /**
     * Update the user's notification settings.
     */
    // app/Http/Controllers/ProfileController.php

    public function updateNotificationSettings(Request $request): RedirectResponse
    {
        $request->validate([
            'preferences' => ['nullable', 'array'],
        ]);

        $user = $request->user();
        $submittedPreferences = $request->input('preferences', []);

        $finalPreferences = [
            'task_reminders_email' => isset($submittedPreferences['task_reminders_email']),
            'calendar_event'       => isset($submittedPreferences['calendar_event']),
            'activity_history'     => isset($submittedPreferences['activity_history']),
        ];

        // এই লাইনটি যোগ করুন
        // dd($finalPreferences);

        // ডাটাবেসে পুরনো ডেটা পুরোপুরি রিপ্লেস করা হচ্ছে।
        // $user->update(['preferences' => $finalPreferences]);
        $user->preferences = $finalPreferences;
        $user->save();

        return Redirect::route('profile.edit')->with('status', 'settings-updated');
    }

    public function verificationStatus(Request $request)
    {
        return response()->json([
            'is_verified' => $request->user()->hasVerifiedEmail()
        ]);
    }
}