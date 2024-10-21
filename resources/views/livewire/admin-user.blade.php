<div>
    <x-card>
        <x-spacing>
            <div class="mb-2 md:mb-4 flex items-center">
                <x-button wire:click="openForm()">Agregar Nuevo Usuario</x-button>
                <form class="shadow-lg ml-3">
                    <label for="default-search"
                        class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Buscar</label>
                    <div class="relative">
                        <div
                            class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none text-primary">
                            <i class="fa fa-search"></i>
                        </div>
                        <x-input type="search" wire:model.live="search" id="default-search" class="pl-10"
                            autocomplete="off" placeholder="Buscar" required />
                    </div>
                </form>
            </div>
            <x-table>
                <x-slot name="thead">
                    <tr>
                        <x-th value="Nombre" />
                        <x-th value="Email" />
                        <x-th value="Rol" />
                        <x-th value="" class="text-center" />
                    </tr>
                </x-slot>
                <x-slot name="tbody">
                    @if ($users && $users->count()>0)
                        @foreach ($users as $user)
                            <x-tr>
                                <x-th value="{{ $user->name }}" />
                                <x-th value="{{ $user->email }}" />
                                <x-th value="{{ $user->role->name ?? __('N/A') }}" />
                                <x-td class="text-center">
                                    <div class="flex items-center justify-center">
                                        <x-button wire:click="edit('{{ $user->code }}')">
                                            <i class="fa fa-pencil mr-2"></i> {{ __('Edit') }}
                                        </x-button>
                                        @if ($user->role_id != 1)
                                            <x-danger-button
                                                wire:confirm="{{ __('Are you sure you want to delete this user?') }}"
                                                wire:click="delete('{{ $user->code }}')" class="ml-1">
                                                <i class="fa fa-remove mr-2"></i> {{ __('Delete') }}
                                            </x-danger-button>
                                        @endif
                                    </div>
                                </x-td>
                            </x-tr>
                        @endforeach
                    @else
                        <x-tr>
                            <x-td colspan="4">Ningún Usuario Encontrado</x-td>
                        </x-tr>
                    @endif
                </x-slot>
            </x-table>
            <div class="mt-4">
                {{ $users->links() }}
            </div>
        </x-spacing>
    </x-card>
    <x-dialog-modal wire:model="isFormOpen" maxWidth="2xl">
        <x-slot name="title">
            Agregar Nuevo Usuario
        </x-slot>

        <x-slot name="content">
            <form wire:submit.prevent="store">
                <div class="grid grid-cols-5 gap-4">
                    <div class="col-span-5 md:col-span-3">
                        <div>
                            <x-label for="name">Nombre</x-label>
                            <x-input type="text" wire:keydown.enter="store" wire:model="name" id="name" />
                            <x-input-error for="name" />
                        </div>
                        <div class="mt-3">
                            <x-label for="email">Email</x-label>
                            <x-input type="email" wire:keydown.enter="store" wire:model="email" id="email" />
                            <x-input-error for="email" />
                        </div>
                        <div class="mt-3">
                            <x-label for="password">Clave</x-label>
                            <x-input type="password" wire:keydown.enter="store" wire:model="password" id="password" />
                            <x-input-error for="password" />
                        </div>
                        <div class="mt-3">
                            <x-label for="role_id">Rol</x-label>
                            <x-select wire:model="role_id" id="role_id">
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </x-select>
                            <x-input-error for="role_id" />
                        </div>
                    </div>
                    <div class="col-span-5 md:col-span-2">
                        <x-h3>Asignar usuario a una tienda</x-h3>
                        <ul>
                            @if($branches && $branches->count()>0)
                            @foreach ($branches as $branch)
                            <li class="w-full">
                                <div class="flex items-center ps-3">
                                    <input id="assignedBranch{{ $branch->id }}" type="checkbox" wire:model="assignedBranch" value="{{ $branch->id }}" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                                    <label for="assignedBranch{{ $branch->id }}" class="py-3 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">{{ mb_strtoupper($branch->name) }}</label>
                                </div>
                            </li>
                            @endforeach
                            <x-input-error for="assignedBranch" />
                            @endif
                        </ul>
                    </div>
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
