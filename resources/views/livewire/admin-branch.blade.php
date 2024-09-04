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
                    @if ($branches->count())
                        @foreach ($branches as $branch)
                            <x-tr>
                                <x-th value="{{ $branch->company->name }}" />
                                <x-th value="{{ $branch->name }}" />
                                <x-th value="{{ $branch->address }}" />
                                <x-td class="text-center">
                                    <div class="flex items-center justify-center">
                                        <x-button wire:click="edit('{{ $branch->code }}')">
                                            <i class="fa fa-pencil mr-2"></i> Editar
                                        </x-button>

                                        <x-danger-button
                                            wire:confirm="¿Estás seguro de que deseas eliminar esta sucursal?"
                                            wire:click="delete('{{ $branch->code }}')" class="ml-1">
                                            <i class="fa fa-remove mr-2"></i> Eliminar
                                        </x-danger-button>
                                    </div>

                                </x-td>
                            </x-tr>
                        @endforeach
                    @else
                        <x-tr>
                            <x-td colspan="4">No se encontraron sucursales.</x-td>
                        </x-tr>
                    @endif
                </x-slot>
            </x-table>
        </x-spacing>
    </x-card>
    <x-dialog-modal wire:model="isFormOpen" maxWidth="lg">
        <x-slot name="title">
            Añadir Nueva Sucursal
        </x-slot>

        <x-slot name="content">
            <form wire:submit.prevent="store">
                <div>
                    <x-label for="name">Nombre</x-label>
                    <x-input type="text" wire:keydown.enter="store" wire:model="name" id="name" />
                    <x-input-error for="name" />
                </div>
                <div class="mt-3">
                    <x-label for="address">Dirección</x-label>
                    <x-input type="text" wire:keydown.enter="store" wire:model="address" id="address" />
                    <x-input-error for="address" />
                </div>
                <div class="mt-3">
                    <x-label for="company_id">Compañía</x-label>
                    <x-select wire:model="company_id" id="company_id">
                        @foreach ($companies as $company)
                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                        @endforeach
                    </x-select>
                    <x-input-error for="company_id" />
                </div>
            </form>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button type="button" wire:click="closeForm"
                class="mr-2">Cancelar</x-secondary-button>
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
