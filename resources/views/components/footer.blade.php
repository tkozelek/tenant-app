<footer class="bg-white/80 dark:bg-gray-900/80 backdrop-blur-md border-t border-gray-200 dark:border-gray-800 mt-auto">
    <div class="container mx-auto px-4 py-6 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center space-x-2">
                <x-application-logo class="h-6 w-auto text-indigo-600 dark:text-indigo-400" />
                <span class="font-semibold text-gray-700 dark:text-gray-300">{{ config('app.name') }}</span>
            </div>
            <div class="text-sm text-gray-500 dark:text-gray-400">
                &copy; {{ date('Y') }} {{ config('app.name') }}. Všetky práva vyhradené.
            </div>
        </div>
    </div>
</footer>
