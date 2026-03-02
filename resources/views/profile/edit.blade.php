<x-layouts.admin>
    <x-header>
        {{ __('Profile') }}
    </x-header>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-profile_wrapper>
                @include('profile.partials.update-profile-information-form')
            </x-profile_wrapper>

            <x-profile_wrapper>
                @include('profile.partials.update-password-form')
            </x-profile_wrapper>

            <x-profile_wrapper>
                @include('profile.partials.delete-user-form')
            </x-profile_wrapper>
        </div>
    </div>
</x-layouts.admin>
