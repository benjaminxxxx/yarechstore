<button {{ $attributes->merge(['type' => 'button', 'class' => 'bg-indigo-600 hover:bg-indigo-700 text-white flex items-center dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 p-2 h-8 focus:ring-0 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
