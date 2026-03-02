<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard</title>
    @include('layouts.partials._meta')
    @include('layouts.partials._imports')
    <title>@yield('title', config('app.name', 'Multi-tenancy App'))</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @yield('head')
    @stack('styles')
</head>
<body class="font-sans antialiased bg-white dark:bg-neutral-950 text-neutral-900 dark:text-neutral-100">

<div x-data="{ sidebarOpen: false }" class="flex h-screen overflow-hidden">

    <x-sidebar.index>

        <x-sidebar.link href="{{ route('dashboard.index') }}" :active="request()->is('admin/dashboard')">
            <x-slot:icon>
                <i class="fa-solid fa-house"></i>
            </x-slot:icon>
            Dashboard
        </x-sidebar.link>

        <x-sidebar.dropdown title="User Management" :active="request()->is('admin/users*')">
            <x-slot:icon>
                <i class="fa-regular fa-user"></i>
            </x-slot:icon>

            <x-sidebar.link href="/admin/users" :active="request()->is('admin/users')">
                All Users
            </x-sidebar.link>
            <x-sidebar.link href="/admin/users/roles" :active="request()->is('admin/users/roles')">
                Roles & Permissions
            </x-sidebar.link>
        </x-sidebar.dropdown>

        <x-sidebar.link href="{{ route('profile.edit') }}" :active="request()->routeIs('profile.edit')">
            <x-slot:icon>
                <i class="fa-solid fa-gear"></i>
            </x-slot:icon>
            Nastavenia
        </x-sidebar.link>

    </x-sidebar.index>

    <div class="flex flex-col flex-1 w-full overflow-y-auto overflow-x-hidden">

        @isset($header)
            <header class="bg-white/80 dark:bg-neutral-900/80 backdrop-blur-md sticky top-0 z-30 border-b border-neutral-200 dark:border-neutral-800">
                <div class="mx-auto py-5 px-4 sm:px-6 lg:px-8">
                    <h1 class="text-xl font-semibold text-neutral-900 dark:text-white leading-tight">
                        {{ $header }}
                    </h1>
                </div>
            </header>
        @endisset

        <main class="p-6">
            {{ $slot }}
        </main>

    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>

@stack('scripts')
</body>
</html>
