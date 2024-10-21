<div>
    <x-card>
        <x-spacing>
            <div class="mb-2 md:mb-4">
                <x-button wire:click="openForm()">Añadir Nueva Sucursal</x-button>
            </div>
            <x-table>
                <x-slot name="thead">
                    <tr>
                        <x-th value="Compañía" />
                        <x-th value="Sucursal" />
                        <x-th value="Dirección" />
                        <x-th value="Acciones" class="text-center" />
                    </tr>
                </x-slot>
                <x-slot name="tbody">
                    {{$proveedores}}
                </x-slot>
            </x-table>
        </x-spacing>
    </x-card>
   
</div>
