@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-normal text-gray-900 dark:text-white']) }}>
    {{ $value ?? $slot }}
</label>
