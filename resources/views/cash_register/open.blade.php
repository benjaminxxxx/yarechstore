<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Open Cash Register') }}
        </h2>
    </x-slot>

    <div class="h-full">
        <livewire:open-cash-register/>
    </div>
</x-app-layout>
