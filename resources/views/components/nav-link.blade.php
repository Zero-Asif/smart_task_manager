@props(['active'])

@php
$baseClasses = 'relative inline-flex items-center px-1 pt-1 text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out';
$activeClasses = 'border-b-2 border-indigo-400 dark:border-indigo-600 text-gray-900 dark:text-gray-100 focus:border-indigo-700';

// ইনঅ্যাকটিভ লিংকের জন্য হোভার ইফেক্ট সহ ক্লাস এবং অ্যানিমেটেড আন্ডারলাইন
$inactiveClasses = 'border-b-2 border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 focus:text-gray-700 dark:focus:text-gray-300 focus:border-gray-300 dark:focus:border-gray-700';
$underlineAnimationClasses = 'after:absolute after:inset-x-0 after:bottom-[-2px] after:h-0.5 after:bg-indigo-500 dark:after:bg-indigo-400 after:origin-center after:scale-x-0 after:transition-transform after:duration-300 after:ease-out hover:after:scale-x-100';


$classes = ($active ?? false)
            ? $baseClasses . ' ' . $activeClasses
            : $baseClasses . ' ' . $inactiveClasses . ' ' . $underlineAnimationClasses;
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>