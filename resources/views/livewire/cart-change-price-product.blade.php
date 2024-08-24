<div>
    <x-dialog-modal wire:model="isFormOpen" maxWidth="lg">
        <x-slot name="title">
            Personalizar el precio para esta venta
        </x-slot>

        <x-slot name="content">
            <x-label class="font-semibold my-3">
                El precio solo se cambiará para esta venta, no afecta las ventas posteriores
            </x-label>
            <form wire:submit.prevent="store">
                <div>
                    <x-label class="my-3">
                        @if ($item)
                            {{ $item->product_name }}
                        @endif
                    </x-label>
                </div>
                <div class="mt-3">
                    <x-label for="product_price">Cambiar Precio</x-label>
                    <x-input type="number" wire:keydown.enter="store" wire:model="product_price" id="product_price" />
                    <x-input-error for="product_price" />
                </div>

            </form>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button type="button" wire:click="closeForm" class="mr-2">Cancelar</x-secondary-button>
            <x-button type="submit" wire:click="store" class="ml-3">Actualizar Precio</x-button>
        </x-slot>
    </x-dialog-modal>
</div>
