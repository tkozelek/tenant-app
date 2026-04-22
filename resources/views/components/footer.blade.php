<footer class="bg-white dark:bg-neutral-950 border-t border-neutral-200 dark:border-neutral-800 mt-auto">
    <div class="container mx-auto px-4 py-6 sm:px-6 lg:px-6">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center opacity-80 hover:opacity-100 transition-opacity">
                <x-application-logo class="h-8 w-auto fill-current text-neutral-900 dark:text-white" />
            </div>

            <div class="text-sm text-neutral-500 dark:text-neutral-400">
                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </div>
        </div>
    </div>
</footer>
