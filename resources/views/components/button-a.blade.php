<a {{ $attributes->merge([ 'class' => 'inline-flex items-center px-4 py-3 bg-indigo-600 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase hover:bg-indigo-700 dark:hover:bg-white focus:bg-indigo-700 dark:focus:bg-white active:bg-indigo-700 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-50 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</a>
