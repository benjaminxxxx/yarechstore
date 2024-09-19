<div>
    <x-loading wire:loading wire:target="sunat" />
    <div class="flex w-full items-center gap-3 my-3">
        <span>
            CLIENTE:
        </span>
        <div class="w-full">
            <div x-data="{ open: false }" class="relative">
                @if ($sale->customer_id)
                    <x-label>
                        {{mb_strtoupper($sale->customer->fullname)}}
                    </x-label>
                @else
                    <x-input type="text" placeholder="Buscar..." wire:model="searchCustomer"
                        wire:keyup="searchingCustomers" x-on:focus="open = true"
                        x-on:blur="setTimeout(() => open = false, 200)" class="w-full" />
                @endif
                @if (strlen($searchCustomer) > 1)
                    <div x-show="open" class="absolute z-10 w-full bg-white border rounded shadow">
                        <ul>
                            @forelse($resultsCustomer as $result)
                                <li wire:click="selectClient({{ $result->id }})" x-on:click="open = false"
                                    class="px-4 py-2 hover:bg-gray-200 cursor-pointer">
                                    {{ $result->fullname }}
                                </li>
                            @empty
                                <li class="px-4 py-2 text-gray-500">No se encontraron resultados.</li>
                            @endforelse
                        </ul>
                    </div>
                @endif
            </div>
        </div>
        <div>
            @if ($sale->customer_id)
                <x-danger-button type="button" wire:click="deleteCostumer">
                    <i class="fa fa-remove"></i>
                </x-danger-button>
            @else
                <x-button type="button" wire:click="createNewCostumer">
                    <i class="fa fa-user-plus"></i>
                    Nuevo
                </x-button>
            @endif

        </div>
    </div>
    <x-dialog-modal wire:model="isFormOpen" maxWidth="full">
        <x-slot name="title">
            Agregar nuevo cliente
        </x-slot>

        <x-slot name="content">
            <form wire:submit.prevent="store" class="grid grid-cols-2 gap-5">
                <div class="col-span-2 md:col-span-1">
                    <x-label for="document_type">{{ __('Tipo de Documento') }}</x-label>
                    
                    <x-select wire:model.live="document_type" id="document_type">
                        @foreach($documentTypes as $type)
                            <option value="{{ $type->code }}">{{ $type->short_name }}</option>
                        @endforeach
                    </x-select>
                    <x-input-error for="document_type" />
                </div>
                <div class="mt-3 col-span-2 md:col-span-1">
                    <x-label for="document_number">{{ __('Número de Documento') }}</x-label>
                    <div class="relative">
                        <x-input type="text" wire:model="document_number" id="document_number" />
                        @if ($document_type == '6')
                            <x-button type="button" wire:click="sunat" class="absolute right-0 top-0">
                                Buscar en la Sunat
                            </x-button>
                        @endif
                    </div>
                    <x-input-error for="document_number" />
                </div>
                <div class="mt-3 col-span-2 md:col-span-1">
                    <x-label for="name">{{ __('Nombre/Razón Social') }}</x-label>
                    <x-input type="text" wire:model="name" id="name" />
                    <x-input-error for="name" />
                </div>
                <div class="mt-3 col-span-2 md:col-span-1">
                    <x-label for="address">{{ __('Dirección') }}</x-label>
                    <x-input type="text" wire:model="address" id="address" />
                    <x-input-error for="address" />
                </div>
                <div class="mt-3 col-span-2 md:col-span-1">
                    <x-label for="department">{{ __('Departamento') }}</x-label>
                    <x-input type="text" wire:model="department" id="department" />
                    <x-input-error for="department" />
                </div>
                <div class="mt-3 col-span-2 md:col-span-1">
                    <x-label for="province">{{ __('Provincia') }}</x-label>
                    <x-input type="text" wire:model="province" id="province" />
                    <x-input-error for="province" />
                </div>
                <div class="mt-3 col-span-2 md:col-span-1">
                    <x-label for="district">{{ __('Distrito') }}</x-label>
                    <x-input type="text" wire:model="district" id="district" />
                    <x-input-error for="district" />
                </div>
                <div class="mt-3 col-span-2 md:col-span-1">
                    <x-label for="phone">{{ __('Teléfonos') }}</x-label>
                    <x-input type="text" wire:model="phone" id="phone" />
                    <x-input-error for="phone" />
                </div>
                <div class="mt-3 col-span-2 md:col-span-1">
                    <x-label for="commercial_name">{{ __('Nombre Comercial') }}</x-label>
                    <x-input type="text" wire:model="commercial_name" id="commercial_name" />
                    <x-input-error for="commercial_name" />
                </div>
                <div class="mt-3 col-span-2 md:col-span-1">
                    <x-label for="email">{{ __('Email') }}</x-label>
                    <x-input type="text" wire:model="email" id="email" />
                    <x-input-error for="email" />
                </div>
                <!-- Otros campos que consideres necesarios -->
            </form>
            <div class="mt-5">
                <x-table >
                    <x-slot name="thead">
                        <tr>
                            <x-th value="{{ __('Nombre/Razón Social') }}" />
                            <x-th value="{{ __('Documento') }}" />
                            <x-th value="{{ __('Dirección') }}" />
                            <x-th value="{{ __('Ubicación') }}" />
                            <x-th value="{{ __('Teléfonos') }}" />
                            <x-th value="{{ __('Email') }}" />
                            <x-th value="{{ __('Acciones') }}" class="text-center" />
                        </tr>
                    </x-slot>
                    <x-slot name="tbody">
                        @if ($customers->count())
                            @foreach ($customers as $customer)
                                <x-tr>
                                    <x-td>{{ $customer->fullname }}</x-td>
                                    <x-td>{!! $customer->documentType->short_name . '<br/> ' . $customer->document_number  !!}</x-td>
                                    <x-td>{{ $customer->address }}</x-td>
                                    <x-td>{{ $customer->department . ' / ' . $customer->province . ' / ' . $customer->district }}</x-td>
                                    <x-td>{{ $customer->phone }}</x-td>
                                    <x-td>{{ $customer->email }}</x-td>
                                    <x-td class="text-center">
                                        
                                        <div class="flex items-center justify-center">
                                            <x-button wire:click="select({{ $customer->id }})" class="ml-1">
                                                <i class="fa fa-check mr-2"></i> {{ __('Seleccionar') }}
                                            </x-button>
                                            @if($customerId!=$customer->id)
                                            <x-button wire:click="edit({{ $customer->id }})" class="ml-1">
                                                <i class="fa fa-pencil mr-2"></i> {{ __('Editar') }}
                                            </x-button>
                
                                            <x-danger-button
                                                wire:confirm="{{ __('¿Estás seguro que deseas eliminar este cliente?') }}"
                                                wire:click="delete({{ $customer->id }})" class="ml-1">
                                                <i class="fa fa-remove mr-2"></i> {{ __('Eliminar') }}
                                            </x-danger-button>

                                            @endif
                                        </div>
                                       
                                    </x-td>
                                </x-tr>
                            @endforeach
                        @else
                            <x-tr>
                                <x-td colspan="6">{{ __('No se encontraron clientes.') }}</x-td>
                            </x-tr>
                        @endif
                    </x-slot>
                </x-table>
            </div>
           
            
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button type="button" wire:click="closeForm"
                class="mr-2">{{ __('Cancelar') }}</x-secondary-button>
            <x-button type="submit" wire:click="store" class="ml-3">{{ __('Guardar') }}</x-button>
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
