<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-5 py-4 bg-white dark:bg-gray-800 rounded-md font-bold text-md text-slate-500 dark:text-gray-300 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
