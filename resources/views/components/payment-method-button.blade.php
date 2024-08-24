<button {{ $attributes->merge(['class' => 'flex items-center p-4 bg-white hover:bg-slate-200 w-full border border-slate-200 focus:outline-none']) }}>
    <i class="fa {{ $icon }} mr-2 w-10 text-3xl text-slate-600"></i> {{ $label }}
</button>