<div>
    <x-card>
        <x-spacing>
            <div class="mb-2 md:mb-4 flex justify-between items-center">
                <x-button wire:click="openForm()">Añadir Nueva Unidad</x-button>
                <!-- Filtro o búsqueda opcional -->
                <input type="text" placeholder="Buscar..." class="p-2 border rounded">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @if ($units->count())
                    @foreach ($units as $unit)
                        <div class="bg-white shadow-md rounded-lg p-4 flex flex-col items-center">
                            <div class="text-lg font-semibold mb-2">{{ $unit->name }}</div>
                            <div class="flex space-x-2">
                                <x-button wire:click="edit({{ $unit->id }})">
                                    <i class="fa fa-pencil mr-1"></i> Editar
                                </x-button>
                                @if ($unit->products->count() == 0)
                                    <x-danger-button wire:confirm="¿Estás seguro de que deseas eliminar esta unidad?"
                                        wire:click="delete({{ $unit->id }})">
                                        <i class="fa fa-remove mr-1"></i> Eliminar
                                    </x-danger-button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-span-full text-center text-gray-500">
                        No se encontraron unidades.
                    </div>
                @endif
            </div>
        </x-spacing>
    </x-card>


    <x-dialog-modal wire:model="isFormOpen" maxWidth="lg">
        <x-slot name="title">
            Añadir Nueva Unidad
        </x-slot>

        <x-slot name="content">
            <form wire:submit.prevent="store">
                <div>
                    <x-label for="name">Nombre</x-label>
                    <x-input type="text" wire:keydown.enter="store" wire:model="name" id="name" />
                    <x-input-error for="name" />
                </div>
            </form>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button type="button" wire:click="closeForm" class="mr-2">Cancelar</x-secondary-button>
            <x-button type="submit" wire:click="store" class="ml-3">Guardar</x-button>
        </x-slot>
    </x-dialog-modal>

    @if (session()->has('message'))
        <x-toast class="bg-green-600">
            {{ session('message') }}
        </x-toast>
    @endif
    @if (session()->has('error'))
        <x-toast class="bg-red-600">
            {{ session('error') }}
        </x-toast>
    @endif
</div>
