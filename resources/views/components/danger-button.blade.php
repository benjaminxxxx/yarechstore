<button {{ $attributes->merge(['type' => 'button', 'class' => 'font-bold text-md inline-flex items-center px-4 py-3 bg-red-600 dark:bg-gray-200 border border-transparent rounded-md text-white dark:text-gray-800 uppercase hover:bg-red-700 dark:hover:bg-white focus:bg-red-700 dark:focus:bg-white active:bg-red-700 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-50 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
