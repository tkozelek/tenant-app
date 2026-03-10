<x-app-layout>
    @section('head')
        <x-head.tinymce-config/>
    @endsection
    <x-header>{{ isset($shop) ? 'Upraviť obchod' : 'Vytvor nový obchod' }}</x-header>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-gray-300 dark:bg-neutral-900 shadow sm:rounded-lg">
                <div class="">
                    @include('tenant.partials.create-tenant-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
