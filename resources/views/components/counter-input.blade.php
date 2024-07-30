@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'bg-indigo-600 bg-indigo-600 text-white p-0 w-[2rem] placeholder-indigp-200 border-x-0 border-indigo-700 h-8 text-center text-gray-900 focus:ring-0 focus:outline-none block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500']) !!}>
