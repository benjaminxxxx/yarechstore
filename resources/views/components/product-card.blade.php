<a {{ $attributes->merge(['class' => 'bg-white hover:bg-primary text-secondary hover:text-white p-3 cursor-pointer rounded-lg overflow-hidden col-span-6 md:col-span-4 lg:col-span-3']) }}>
    <div class="text-center">
        <img src="{{ $photoUrl }}" class="w-auto" alt="{{ $name }}" />
        <p class="py-2"><b>{{ $name }}</b></p>
        <p><b>S/. {{ $finalPrice }}</b></p>
    </div>
</a>