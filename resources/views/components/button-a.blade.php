<a {{ $attributes->merge([ 'class' => 'inline-flex items-center px-4 py-3 bg-secondary dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white  uppercase hover:bg-opacity-80 dark:hover:bg-white focus:bg-opacity-80 active:bg-opacity-80 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-secondary focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-50 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</a>
