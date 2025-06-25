@props(['user'])

<div class="w-full max-w-md p-8 space-y-4 bg-white dark:bg-gray-800 rounded-2xl shadow-2xl text-center
            transition-all duration-300 ease-in-out hover:scale-105 hover:shadow-lg dark:hover:shadow-2xl dark:hover:shadow-cyan-500/20">

    @if ($user)
        {{-- এই অংশটি শুধুমাত্র লগইন করা ব্যবহারকারীরা দেখতে পাবে --}}
        
        <div class="mb-4">
            @if($user->gender == 'male')
                <span class="text-7xl" role="img" aria-label="Male user icon">👨</span>
            @elseif($user->gender == 'female')
                <span class="text-7xl" role="img" aria-label="Female user icon">👩</span>
            @else
                {{-- Default Smiley Icon --}}
                <span class="text-7xl animate-pulse" role="img" aria-label="Smiley face icon">🙂</span>
            @endif
        </div>

        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-200">
            Welcome, {{ $user->name }} to {{ config('app.name', 'Remind me!') }}!
        </h1>
        <p class="mt-2 text-gray-600 dark:text-gray-400">
            Your account is ready. Let's get started managing your tasks.
        </p>
        
        <div class="pt-6 space-y-4">
            <a href="{{ route('dashboard') }}"
               class="inline-block w-full px-4 py-3 bg-green-600 border border-transparent rounded-md font-semibold text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                Go to Dashboard
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                   class="inline-block w-full px-4 py-3 bg-red-600 border border-transparent rounded-md font-semibold text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    {{ __('Log Out') }}
                </button>
            </form>
        </div>

    @else
        {{-- এই অংশটি লগইন না করা ব্যবহারকারীরা (Guest) দেখতে পাবে --}}
        <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-200">
            Welcome to {{ config('app.name', 'Remind me!') }}
        </h1>
        <p class="mt-2 text-gray-600 dark:text-gray-400">
            Your personal task management solution.
        </p>
        <div class="mt-8 space-x-4">
            <a href="{{ route('login') }}"
               class="inline-block px-6 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-white hover:bg-indigo-700 transition">
                Log in
            </a>
            <a href="{{ route('register') }}"
               class="inline-block px-6 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-white hover:bg-gray-700 transition">
                Register
            </a>
        </div>
    @endif
</div>