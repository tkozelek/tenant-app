<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @include('layouts.partials._meta')
    @include('layouts.partials._imports')
    <title>@yield('title', config('app.name', 'Multi-tenancy App'))</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @yield('head')
    @stack('styles')
</head>
<body class="font-sans antialiased bg-white dark:bg-neutral-950 text-neutral-900 dark:text-neutral-100">

<div class="min-h-screen flex flex-col">

    @include('layouts.navigation')

    @isset($header)
        <header class="bg-white/80 dark:bg-neutral-900/80 backdrop-blur-md sticky top-0 z-30 border-b border-neutral-200 dark:border-neutral-800">
            <div class="container mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <h1 class="text-xl font-semibold text-neutral-900 dark:text-white leading-tight">
                    {{ $header }}
                </h1>
            </div>
        </header>
    @endisset

    <main class="grow">
        {{ $slot }}
    </main>

    <x-footer/>

</div>

<x-flash-message/>

<script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>

@stack('scripts')
</body>
</html>
