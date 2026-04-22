<x-auth-layout>
    <div class="mb-10 text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 mb-4 rounded-md bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400">
            <i class="fa-solid fa-lock text-2xl"></i>
        </div>
        <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
            Vitajte späť
        </h2>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <!-- Email -->
        <div class="space-y-1">
            <x-input label="E-mailová adresa" name="email" fa-icon="envelope" type="email" :value="old('email')" required autofocus autocomplete="username" placeholder="vas@email.sk" />
        </div>

        <!-- Password -->
        <div class="space-y-1">
            <div class="flex items-center justify-between mb-1">
                <x-input-label for="password" value="Heslo" />
                @if (Route::has('password.request'))
                    <a class="text-xs font-semibold text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors" href="{{ route('password.request') }}">
                        Zabudli ste heslo?
                    </a>
                @endif
            </div>
            <x-text-input
                id="password"
                icon="eye"
                class="block w-full"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                placeholder="••••••••"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember -->
        <div class="flex items-center">
            <x-checkbox
                label="Zapamätať si ma na tomto zariadení"
                name="remember"
            />
        </div>

        <div class="pt-2">
            <x-primary-button class="w-full flex justify-center !py-4 !text-base !font-bold !rounded-md shadow-sm">
                Prihlásiť sa <i class="fa-solid fa-arrow-right ml-2 text-sm"></i>
            </x-primary-button>
        </div>

        <div class="text-center">
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Nemáte ešte účet?
                <a href="{{ route('register') }}" class="font-bold text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors">
                    Vytvorte si ho teraz
                </a>
            </p>
        </div>
    </form>
</x-auth-layout>


