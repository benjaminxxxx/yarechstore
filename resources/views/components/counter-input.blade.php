@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'bg-secondary text-white p-0 w-[2rem] border-x-0 border-secondary h-8 text-center text-gray-900 focus:ring-0 focus:outline-none block']) !!}>
