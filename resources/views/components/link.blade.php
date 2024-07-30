@props(['active'])

@php
    $classes =
        $active ?? false
            ? 'flex items-center font-semibold py-4 px-3 text-indigo-700 rounded-lg dark:text-white bg-slate-100 hover:bg-slate-100 dark:hover:bg-gray-700 group'
            : 'flex items-center font-semibold py-4 px-3 text-gray-700 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group';
@endphp

<li><a {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
</li>
