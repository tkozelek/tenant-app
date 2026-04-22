<x-auth-layout>
    <div class="mb-10 text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 mb-4 rounded-md bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400">
            <i class="fa-solid fa-user-plus text-2xl"></i>
        </div>
        <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
            Vytvorte si účet!
        </h2>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-input
                label="Meno"
                id="first_name"
                name="first_name"
                type="text"
                :value="old('first_name')"
                required
                autofocus
                autocomplete="given-name"
                placeholder="Meno"
            />
            <x-input
                label="Priezvisko"
                id="last_name"
                name="last_name"
                type="text"
                :value="old('last_name')"
                required
                autocomplete="family-name"
                placeholder="Priezvisko"
            />
        </div>

        <x-input
            label="E-mailová adresa"
            id="email"
            name="email"
            type="email"
            :value="old('email')"
            required
            autocomplete="username"
            placeholder="meno@email.com"
        />

        <x-input
            label="Heslo"
            id="password"
            name="password"
            type="password"
            required
            autocomplete="new-password"
            placeholder="••••••••"
        />

        <x-input
            label="Potvrdenie hesla"
            id="password_confirmation"
            name="password_confirmation"
            type="password"
            required
            autocomplete="new-password"
            placeholder="••••••••"
        />

        <div class="pt-4">
            <x-primary-button class="w-full flex justify-center !py-3.5 !text-base !font-bold !rounded-md shadow-sm">
                Registrovať sa
            </x-primary-button>
        </div>

        <div class="text-center text-sm text-gray-600 dark:text-gray-400">
            Už máte účet?
            <a class="font-semibold text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300 hover:underline transition-colors" href="{{ route('login') }}">
                Prihláste sa
            </a>
        </div>
    </form>
</x-auth-layout>
