
<button
    {{ $attributes->merge([
        'type' => 'button',
        'class' => 'inline-flex items-center px-4 py-3 bg-secondary dark:bg-secondaryDark border border-transparent rounded-md font-semibold text-xs text-secondaryText uppercase hover:bg-opacity-80 focus:outline-none focus:ring-2 focus:ring-secondary focus:ring-offset-2 disabled:opacity-50 transition ease-in-out duration-150'
    ]) }}
>
    {{ $slot }}
</button>