<div {{ $attributes->merge(['class' => 'bg-white text-primary p-3 rounded-lg overflow-hidden col-span-6 md:col-span-4 lg:col-span-3']) }}>
    <div class="text-center">
        <img src="{{ $photoUrl }}" class="w-auto" alt="{{ $name }}" />
        <p class="py-2"><b>{{ $name }}</b></p>
        <p class=""><b>S/. {{ $finalPrice }}</b></p>
        @foreach ($presentations as $presentation)
            <x-button class="w-full mt-3 justify-center" wire:click="addToCart('{{ $code }}', '{{ $presentation->unit }}', {{ $presentation->factor }}, {{ $presentation->price }})">
                {{ $presentation->units->name }} x{{ $presentation->factor }} a S/. {{ $presentation->price }}
            </x-button>
        @endforeach
    </div>
</div>