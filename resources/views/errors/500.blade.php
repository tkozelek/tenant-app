<x-error-layout>
    <x-error-main-text
        error-code="500"
        :title="__('Server Error')"
    >
        {{ __('Server Error') }}
    </x-error-main-text>

    <x-error-buttons/>

    <x-error-bug-report/>
</x-error-layout>




