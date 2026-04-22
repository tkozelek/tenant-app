@props(['disabled' => false, 'icon' => null])

<div class="relative">
    @if($icon)
        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400 dark:text-gray-500">
            <i class="fa-regular fa-{{ $icon }}"></i>
        </div>
    @endif

    <input @disabled($disabled) {{ $attributes->merge([
        'class' => ($icon ? 'pl-10 ' : 'px-4 ') . 'py-3 w-full bg-white dark:bg-neutral-900 border dark:focus:bg-neutral-900 dark:focus:text-white border-gray-300 dark:border-neutral-700 text-gray-900 dark:text-neutral-100 focus:ring-2 rounded-xl shadow-sm transition-all duration-200 ease-in-out placeholder-gray-400 dark:placeholder-gray-500'
    ]) }}>
</div>
