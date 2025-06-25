{{-- Alpine.js কম্পোনেন্ট যোগ করা হয়েছে --}}
<div
    x-data="{
        init() {
            // যদি ব্যবহারকারী ভেরিফাইড না থাকে, তাহলে পোলিং শুরু হবে
            if (!{{ auth()->user()->hasVerifiedEmail() ? 'true' : 'false' }}) {
                let interval = setInterval(() => {
                    fetch('{{ route('api.user.verification-status') }}')
                        .then(response => response.json())
                        .then(data => {
                            if (data.is_verified) {
                                // যদি ভেরিফাইড হয়ে যায়, তাহলে পেজ রিলোড হবে
                                location.reload();
                                // ইন্টারভ্যাল বন্ধ করে দেওয়া হবে
                                clearInterval(interval);
                            }
                        });
                }, 5000); // প্রতি ৫ সেকেন্ড পর পর চেক করবে
            }
        }
    }"
    x-init="init()"
    class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg"
>
    <div class="flex flex-col md:flex-row items-center gap-4 text-center md:text-left">
        <div class="flex-shrink-0">
            <svg class="h-12 w-12 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
        </div>
        <div class="flex-grow">
            <h3 class="font-bold text-gray-900 dark:text-gray-100">Unlock More Features!</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                Verify your email to get task reminders, sync with Google Calendar, and secure your activity history.
            </p>
        </div>
        <div class="flex-shrink-0 mt-4 md:mt-0">
            <a href="{{ route('profile.edit') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                Go to Settings
            </a>
        </div>
    </div>
</div>