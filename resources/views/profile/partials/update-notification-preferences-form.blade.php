<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            Notification and Data Preferences
        </h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Manage how you receive reminders and other notifications. Email verification is required to enable these options.
        </p>
    </header>

    {{-- Alpine.js কম্পোনেন্ট শুরু: ক্যালেন্ডার অপশনের অবস্থা (state) এখানে ম্যানেজ করা হবে --}}
    <form
        method="post"
        action="{{ route('profile.settings.update') }}"
        class="mt-6 space-y-6"
        x-data="{ 
            isCalendarEnabled: {{ (Auth::user()->preferences['calendar_event'] ?? false) ? 'true' : 'false' }} 
        }">
        @csrf
        @method('patch')

        <div class="space-y-4">
            {{-- Notification Options --}}
            <div class="p-4 border rounded-lg border-gray-200 dark:border-gray-700">
                <h3 class="font-semibold text-gray-800 dark:text-gray-200 mb-3">Notification Options</h3>

                {{-- Option 1: Task Reminders via Email --}}
                <label for="task_reminders_email" class="flex items-center">
                    <input
                        id="task_reminders_email"
                        name="preferences[task_reminders_email]"
                        type="checkbox"
                        class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800"
                        @disabled(!$user->hasVerifiedEmail())
                    @checked(old('preferences.task_reminders_email', $user->preferences['task_reminders_email'] ?? false))
                    >
                    <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">Receive Task Reminders via Email</span>
                </label>

                {{-- Option 2: Google Calendar Events --}}
                <div class="mt-2">
                    <label for="calendar_event" class="flex items-center">
                        <input
                            id="calendar_event"
                            name="preferences[calendar_event]"
                            type="checkbox"
                            x-model="isCalendarEnabled"
                            class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800"
                            @disabled(!$user->hasVerifiedEmail())
                        >
                        <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">Automatically Create Google Calendar Events for Tasks</span>
                    </label>

                    {{-- এই সেকশনটি শুধুমাত্র isCalendarEnabled = true হলেই একটি সুন্দর অ্যানিমেশনসহ দেখা যাবে --}}
                    <div
                        x-show="isCalendarEnabled"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform -translate-y-2"
                        x-transition:enter-end="opacity-100 transform translate-y-0"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 transform translate-y-0"
                        x-transition:leave-end="opacity-0 transform -translate-y-2"
                        class="mt-4 pl-8 pr-2 py-3 bg-gray-50 dark:bg-gray-800/50 rounded-lg border-l-4 border-indigo-400">
                        @if(Auth::user()->google_access_token)
                        <div class="flex items-center gap-3">
                            <svg class="h-6 w-6 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Google Calendar is connected.</span>
                        </div>
                        @else
                        <a href="{{ route('google.redirect') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
                            <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="48px" height="48px">
                                <path fill="#fbc02d" d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12	s5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24s8.955,20,20,20	s20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z"></path>
                                <path fill="#e53935" d="M6.306,14.691l6.571,4.819C14.655,15.108,18.961,12,24,12c3.059,0,5.842,1.154,7.961,3.039	l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z"></path>
                                <path fill="#4caf50" d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36	c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.025C9.505,39.556,16.227,44,24,44z"></path>
                                <path fill="#1565c0" d="M43.611,20.083L43.595,20L42,20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.574	l6.19,5.238C39.978,36.218,44,30.608,44,24C44,22.659,43.862,21.35,43.611,20.083z"></path>
                            </svg>
                            Connect with Google
                        </a>
                        @endif
                    </div>
                </div>

                {{-- Option 3: Activity History --}}
                {{-- ফাঁকা রাখা কমেন্টটি সরিয়ে এই অপশনটি যোগ করা হয়েছে --}}
                <label for="activity_history" class="flex items-center mt-2">
                    <input
                        id="activity_history"
                        name="preferences[activity_history]"
                        type="checkbox"
                        class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800"
                        @disabled(!$user->hasVerifiedEmail())
                    @checked(old('preferences.activity_history', $user->preferences['activity_history'] ?? false))
                    >
                    <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">Securely Link Activity History to This Email</span>
                </label>

            </div>
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                Save Preferences
            </button>

            @if (session('status') === 'settings-updated')
            <p
                x-data="{ show: true }"
                x-show="show"
                x-transition
                x-init="setTimeout(() => show = false, 2000)"
                class="text-sm text-gray-600 dark:text-gray-400">Saved.</p>
            @endif
        </div>
    </form>

    

</section>