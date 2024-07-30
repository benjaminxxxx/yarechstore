<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-3 bg-white hover:bg-gray-200  dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 dark:text-gray-800 uppercase dark:hover:bg-white focus:bg-gray-200 dark:focus:bg-white active:bg-gray-200 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-50 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
