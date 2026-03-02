<div x-show="sidebarOpen"
     x-transition.opacity
     @click="sidebarOpen = false"
     class="fixed inset-0 z-20 bg-black/50 lg:hidden"
     style="display: none;"></div>

<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
       class="fixed inset-y-0 left-0 z-30 w-64 overflow-y-auto transition-transform duration-300 transform bg-gray-900 lg:translate-x-0 lg:static lg:inset-auto flex flex-col">

    <div class="flex items-center justify-center py-5 border-b border-gray-800">
        <x-application-logo/>
    </div>

    <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
        {{ $slot }}
    </nav>

    <div class="p-4 border-t border-gray-800">
        <div class="flex items-center gap-3">
            <img class="w-10 h-10 rounded-full" src="https://ui-avatars.com/api/?name=Admin+User&background=6366f1&color=fff" alt="User">
            <div>
                <p class="text-sm font-medium text-white">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</p>
                <p class="text-xs text-gray-400">{{ auth()->user()->email }}</p>
            </div>
        </div>
    </div>
</aside>
