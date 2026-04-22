<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('layouts.partials._meta')
    @include('layouts.partials._imports')
    <title>@yield('title', config('app.name', 'Multi-tenancy App'))</title>
</head>
<body class="font-sans antialiased bg-slate-50 dark:bg-gray-950 text-gray-900 dark:text-gray-100">
<div class="min-h-screen flex flex-col">
    @include('layouts.navigation')

    <!-- Page Heading -->
    @isset($header)
        <header class="bg-white/80 dark:bg-gray-900/80 backdrop-blur-md sticky top-0 z-30 border-b border-gray-200 dark:border-gray-800">
            <div class="container mx-auto py-4 px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between">
                    <h1 class="text-xl font-semibold tracking-tight">
                        {{ $header }}
                    </h1>
                </div>
            </div>
        </header>
    @endisset

    <main class="flex-grow">
        {{ $slot }}
    </main>

    <x-footer/>

    <x-flash-message/>
</div>
</body>
</html>
