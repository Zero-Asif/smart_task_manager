<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

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
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            
            @include('layouts.navigation')

            {{-- Main Content Area --}}
            <div class="pt-16"> {{-- h-16 navbar (4rem = 64px) এর জন্য এই প্যাডিং অপরিহার্য --}}
            
                @if (isset($header))
                    <header class="bg-white dark:bg-gray-800 shadow">
                        @auth
                        @if (Auth::user() && !Auth::user()->hasVerifiedEmail())
                            <div x-data="{ show: true }" x-show="show" x-transition class="bg-blue-100 dark:bg-blue-900/50 border-b border-blue-200 dark:border-blue-700 text-blue-800 dark:text-blue-200">
                                <div class="max-w-7xl mx-auto py-3 px-4 sm:px-6 lg:px-8">
                                    <div class="flex items-center justify-between flex-wrap">
                                        <div class="w-0 flex-1 flex items-center">
                                            <span class="flex p-2 rounded-lg bg-blue-200 dark:bg-blue-800">
                                                <svg class="h-6 w-6 text-blue-700 dark:text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            </span>
                                            <p class="ms-3 font-medium">
                                                <span class="hidden md:inline">To receive task reminders, please check your inbox for a verification link.</span>
                                            </p>
                                        </div>
                                        <div class="order-3 mt-2 w-full flex-shrink-0 sm:order-2 sm:mt-0 sm:w-auto">
                                            <form method="POST" action="{{ route('verification.send') }}">
                                                @csrf
                                                <button type="submit" class="flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-blue-600 dark:text-blue-100 bg-white dark:bg-blue-800 hover:bg-blue-50 dark:hover:bg-blue-700">
                                                    Resend verification email
                                                </button>
                                            </form>
                                        </div>
                                        <div class="order-2 flex-shrink-0 sm:order-3 sm:ms-3">
                                            <button @click="show = false" type="button" class="-me-1 flex p-2 rounded-md hover:bg-blue-200 dark:hover:bg-blue-700">
                                                <span class="sr-only">Dismiss</span>
                                                <svg class="h-6 w-6 text-blue-700 dark:text-blue-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @endauth
                        
                        {{-- মূল হেডার কন্টেন্ট --}}
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endif

                <main>
                    {{ $slot }}
                </main>
            </div>
        </div>
        
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const themeToggleButtons = document.querySelectorAll('#theme-toggle, #theme-toggle-mobile');
                
                function updateIcons(isDarkMode) {
                    document.querySelectorAll('#theme-toggle-dark-icon, #theme-toggle-dark-icon-mobile').forEach(el => isDarkMode ? el.classList.add('hidden') : el.classList.remove('hidden'));
                    document.querySelectorAll('#theme-toggle-light-icon, #theme-toggle-light-icon-mobile').forEach(el => isDarkMode ? el.classList.remove('hidden') : el.classList.add('hidden'));
                }

                function toggleTheme() {
                    const isDarkMode = document.documentElement.classList.toggle('dark');
                    localStorage.theme = isDarkMode ? 'dark' : 'light';
                    updateIcons(isDarkMode);
                    window.dispatchEvent(new CustomEvent('themeChanged', { detail: { isDarkMode } }));
                }

                updateIcons(document.documentElement.classList.contains('dark'));

                themeToggleButtons.forEach(btn => {
                    btn?.addEventListener('click', toggleTheme);
                });
            });
        </script>
        
        {{-- অন্যান্য পেইজ থেকে পাঠানো স্ক্রিপ্ট এখানে লোড হবে --}}
        @stack('scripts')
    </body>
</html>