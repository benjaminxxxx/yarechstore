<button
    {{ $attributes->merge([
        'type' => 'submit',
        'class' => 'inline-flex items-center px-4 py-3 bg-primary dark:bg-primaryDark border border-transparent rounded-md font-semibold text-xs text-primaryText dark:text-primaryDarkText uppercase hover:bg-primaryHoverOpacity dark:hover:bg-primaryDarkHoverOpacity focus:outline-none focus:ring-2 focus:ring-primary dark:focus:ring-primaryDark focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-50 transition ease-in-out duration-150'
    ]) }}
>
    {{ $slot }}
</button>