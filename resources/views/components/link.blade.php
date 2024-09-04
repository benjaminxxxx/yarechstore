@props(['active'])

@php
    $classes =
        $active ?? false
            ? 'flex items-center font-semibold py-3 px-5 text-primaryText rounded-lg dark:text-white bg-primaryHoverOpacity dark:hover:bg-stone-700 group'
            : 'flex items-center font-semibold py-3 px-5 text-primaryTextGray hover:text-primary rounded-lg dark:text-white hover:bg-stone-100 dark:hover:bg-stone-700 group';
@endphp

<li><a {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
</li>
