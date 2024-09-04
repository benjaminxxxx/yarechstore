<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-block p-4 border-b-2 rounded-t-lg']) }}>
    {{ $slot }}
</button>
