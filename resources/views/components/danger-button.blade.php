
<button
    {{ $attributes->merge([
        'type' => 'button',
        'class' => 'inline-flex items-center px-4 py-3 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-primaryText uppercase hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-red-600 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-50 transition ease-in-out duration-150'
    ]) }}
>
    {{ $slot }}
</button>