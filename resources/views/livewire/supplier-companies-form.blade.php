<div>
    <!--DESARROLLO SUPPLIERCOMPANY-->
    <x-dialog-modal-header wire:model="isFormOopen" maxWidth="full">
        <x-slot name="title">
            <div class="flex items-center justify-between">
                <div class="">
                    COMPAÑIA
                </div>
                <div class="flex-shrink-0">
                    <button wire:click="closeForm" class="focus:outline-none">
                        <i class="fa-solid fa-circle-xmark"></i>
                    </button>
                </div>
            </div>
        </x-slot>
        <x-slot name="content">

            <div class="grid grid-cols-2 gap-5">
                <div class="col-span-2 lg:col-span-1">
                    <x-label for="name">Nombre de la Compañia</x-label>
                    <x-input type="text" wire:model="name" />
                    <x-input-error for="name" />
                </div>
                <div class="col-span-2 lg:col-span-1">
                    <x-label for="ruc">Ruc</x-label>
                    <x-input type="text" wire:model="ruc"/>
                    <x-input-error for="ruc" />
                </div>
                <div class="col-span-2 lg:col-span-1">
                    <x-label for="contact_person">Nombre de Contacto</x-label>
                    <x-input type="text" wire:model="contact_person"/>
                    <x-input-error for="contact_person" />
                </div>
                <div class="col-span-2 lg:col-span-1">
                    <x-label for="phone">Teléfono</x-label>
                    <x-input type="text" wire:model="phone" />
                    <x-input-error for="phone" />
                </div>
                <div class="col-span-2 lg:col-span-1">
                    <x-label for="email">Email</x-label>
                    <x-input type="text" wire:model="email"/>
                    <x-input-error for="email" />
                </div>
                <div class="col-span-2 lg:col-span-1">
                    <x-label for="address">Dirección</x-label>
                    <x-input type="text" wire:model="address"/>
                    <x-input-error for="address" />
                </div>
                <div class="col-span-2 lg:col-span-1">
                    <x-label for="whatsapp">Whatsapp</x-label>
                    <x-input type="text" wire:model="whatsapp"/>
                    <x-input-error for="whatsapp" />
                </div>
                
            </div>
        </x-slot>
        <x-slot name="footer">
            
            <x-button-normal type="button" wire:click="closeForm" class="mr-2">Cancelar</x-button-normal>
            <x-button type="button" wire:click="save">Guardar</x-button>
        </x-slot>
    </x-dialog-modal-header>
</div>
