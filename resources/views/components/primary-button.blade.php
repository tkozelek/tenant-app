<button {{ $attributes->merge([
    'type' => 'submit',
    'class' => '
        cursor-pointer inline-flex items-center px-5 py-3
        bg-gray-700 dark:bg-gray-300
        hover:bg-gray-900 dark:hover:bg-gray-400
        border border-transparent
        rounded-md font-semibold text-xs
        text-white dark:text-gray-900
        uppercase tracking-widest
        focus:bg-gray-700 dark:focus:bg-gray-300
        active:bg-gray-900 dark:active:bg-gray-400
        focus:outline-none focus:ring-2
        focus:ring-indigo-500 focus:ring-offset-2
        transition-colors ease-in-out duration-150'
]) }}>
    {{ $slot }}
</button>
