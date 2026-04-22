<div class="pt-2 pb-3 space-y-1">
    <div class="px-4">
        <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</div>
        <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
    </div>
</div>

<div class="pt-1 pb-1 border-t border-gray-200">
    <div class="mt-1">
        @include('layouts.partials.phone.navlinks')
    </div>
</div>
