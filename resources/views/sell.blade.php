<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Sell') }}
        </h2>
    </x-slot>

    <div class="h-full">
        <livewire:sell/>
    </div>
</x-app-layout>
