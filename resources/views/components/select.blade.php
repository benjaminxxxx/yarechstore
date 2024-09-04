@props(['disabled' => false])

<select {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'shadow-sm bg-slate-100 border-none text-gray-900 text-sm rounded-lg focus:ring-primary focus:border-primary block w-full p-2.5 pr-8 dark:border-gray-600 dark:shadow-sm-light']) !!}>
{{$slot}}
</select>
