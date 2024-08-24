<div>
    @if( $this->thereIsOpenCash)
    <div class="flex justify-center">
        <x-button-a href="{{route('dashboard')}}">Iniciar Venta</x-button-a>
        
    </div>
    @else
    <div class="max-w-lg mx-auto mt-8 p-6 bg-white rounded-lg shadow-lg">
        <x-h2 class="text-2xl font-bold mb-4">Abrir Nueva Caja</x-h2>
    
        @if (session()->has('message'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('message') }}</span>
            </div>
        @endif
    
        <form wire:submit.prevent="openRegister">
            <div class="mb-4">
                <x-label for="initialAmount" class="mb-2">Monto Inicial</x-label>
                <x-input type="number" step="0.01"  autofocus id="initialAmount" wire:model="initialAmount" placeholder="Ingrese el monto inicial"/>
                <x-input-error for="initialAmount"/>
            </div>
    
            <div class="flex justify-end">
                <x-button type="submit">
                    Abrir Caja
                </x-button>
            </div>
        </form>
    </div>
    @endif
    
</div>