@props(['disabled' => false,'value'=>null])

<textarea {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'shadow-sm bg-slate-100 border-none text-gray-900 text-sm rounded-lg focus:ring-primary focus:border-primary block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:shadow-sm-light']) !!}>{{$value??$slot}}</textarea>
