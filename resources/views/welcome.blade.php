<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome | {{ config('app.name', 'Remind me!') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="font-sans antialiased">
    {{-- Guest Layout Component ব্যবহার করে --}}
    <x-guest-layout>
        @auth
            {{-- এই অংশটি শুধুমাত্র লগইন করা ব্যবহারকারীরা দেখতে পাবে (যেমন, রেজিস্ট্রেশনের পর) --}}
            <x-ui.welcome-card :user="Auth::user()" />
        @else
            {{-- এই অংশটি লগইন না করা ব্যবহারকারীরা (Guest) দেখতে পাবে --}}
            <div class="text-center">
                <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-200">
                    Welcome to {{ config('app.name', 'Remind me!') }}
                </h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">
                    Your personal task management solution.
                </p>
                <div class="mt-8 space-y-4 sm:space-y-0 sm:space-x-4 flex flex-col sm:flex-row sm:justify-center">
                    <a href="{{ route('login') }}"
                       class="inline-flex items-center justify-center px-6 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                        Log In
                    </a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                           class="inline-flex items-center justify-center px-6 py-3 bg-gray-600 border border-transparent rounded-md font-semibold text-white uppercase tracking-widest hover:bg-gray-700 transition">
                            Register
                        </a>
                    @endif
                </div>

                {{-- নতুন গেস্ট বাটন --}}
                <div class="mt-6">
                    <a href="{{ route('dashboard') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:underline">
                        or Continue as a Guest
                    </a>
                </div>
            </div>
        @endauth
    </x-guest-layout>
</body>
</html>