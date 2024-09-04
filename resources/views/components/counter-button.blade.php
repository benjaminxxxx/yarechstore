<button
    {{ $attributes->merge([
        'type' => 'button',
        'class' => 'bg-secondary hover:bg-secondaryHoverOpacity text-secondaryText dark:bg-secondaryDark dark:hover:bg-secondaryDarkHoverOpacity dark:border-secondaryDark p-2 h-8 focus:ring-0 dark:focus:ring-secondaryDark focus:ring-2 focus:outline-none disabled:opacity-25 transition ease-in-out duration-150'
    ]) }}
>
    {{ $slot }}
</button>