<section x-data="{
    preferences: {{ json_encode(Auth::user()->notification_preferences ?? [
        'email_reminder' => false, 
        'calendar_event' => false, 
        'activity_history' => false
    ]) }},
    isEmailVerified: {{ Auth::user()->hasVerifiedEmail() ? 'true' : 'false' }}
}">
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Notification and Data Preferences') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Manage how you receive reminders and other notifications. Email verification is required to enable these options.") }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.settings.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div class="p-4 border rounded-lg dark:border-gray-700 space-y-3">
            <h3 class="font-medium text-gray-900 dark:text-gray-100">Notification Options</h3>
            
            <template x-if="isEmailVerified">
                <div class="space-y-3">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="preferences[email_reminder]" x-model="preferences.email_reminder" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600">
                        <span class="ms-2 text-sm text-gray-700 dark:text-gray-300">Receive Task Reminders via Email</span>
                    </label>
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="preferences[calendar_event]" x-model="preferences.calendar_event" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600">
                        <span class="ms-2 text-sm text-gray-700 dark:text-gray-300">Automatically Create Google Calendar Events for Tasks</span>
                    </label>
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="preferences[activity_history]" x-model="preferences.activity_history" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600">
                        <span class="ms-2 text-sm text-gray-700 dark:text-gray-300">Securely Link Activity History to This Email</span>
                    </label>
                </div>
            </template>

            <template x-if="!isEmailVerified">
                 <p class="text-sm text-yellow-600 dark:text-yellow-400">
                    Your email is not verified. Please verify your email to enable notification options.
                </p>
            </template>
        </div>
        
        <div class="flex items-center gap-4">
            <x-primary-button :disabled="!Auth::user()->hasVerifiedEmail()">{{ __('Save Preferences') }}</x-primary-button>
             @if (session('status') === 'preferences-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-sm text-gray-600 dark:text-gray-400">{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>