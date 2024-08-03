<div>
    @if (Auth::user()->role_id == 1)
        <x-card>
            <x-spacing>
                <div class="mb-2 md:mb-4 flex items-center">

                    <x-select class="!w-96" wire:model.live="branchCode">
                        <option value="">Selecciona una sucursal primero</option>
                        @if ($branches)
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->code }}">{{ $branch->name }}</option>
                            @endforeach
                        @endif
                    </x-select>

                </div>
            </x-spacing>
        </x-card>
    @endif
    <div class="block lg:flex gap-5 mt-4">
        <div class="flex-1">
            <x-card>
                <x-spacing>
                    <form class="flex w-full shadow-lg">

                        <div class="relative w-full">
                            <div
                                class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none text-indigo-600">
                                <i class="fa fa-search"></i>
                            </div>
                            <x-input type="search" wire:model.live="search" id="default-search" class="w-full pl-10"
                                autocomplete="off" placeholder="{{ __('Search...') }}" required />
                        </div>
                    </form>
                </x-spacing>
            </x-card>
            <x-card class="mt-5">
                <x-spacing>
                    <x-table>
                        <x-slot name="thead">
                            <tr>
                                <x-th value="{{ __('Product Image') }}" />
                                <x-th value="{{ __('Product Name') }}" />
                                <x-th value="{{ __('Location') }}" />
                                <x-th value="{{ __('Stock') }}" />
                                <x-th value="{{ __('Actions') }}" class="text-center" />
                            </tr>
                        </x-slot>
                        <x-slot name="tbody">
                            @if ($inventoryItems->count())
                                @foreach ($inventoryItems as $item)
                                    <x-tr>
                                        <x-td>
                                            <img src="{{ $item->photo_url }}" alt="{{ $item->name }}"
                                                class="w-10 h-10 object-cover">
                                        </x-td>
                                        <x-td>{{ $item->name }}</x-td>
                                        <x-td>{{ $item->location }}</x-td>
                                        <x-td>
                                            @if ($item->inventories->count() == 0)
                                                Sin Stock
                                            @else
                                                {{ $item->inventories[0]->stock }}
                                                {{ $item->unit ? $item->unit->name : '' }}
                                            @endif
                                        </x-td>
                                        <x-td class="text-center">
                                            <div class="flex items-center justify-center">
                                                <x-button wire:click="see('{{ $item->code }}')">
                                                    <i class="fa fa-eye  mr-2"></i> Ver stock
                                                </x-button>

                                                @if ($item->inventories->count() != 0)
                                                    @if ($item->child()->count() > 0)
                                                        @if ($item->inventories[0]->stock > 0)
                                                            <x-success-button class="ml-3"
                                                                wire:click="openBox('{{ $item->code }}')">
                                                                <i class="fa fa-box-open  mr-2"></i> Abrir 1
                                                                {{ $item->unit ? $item->unit->name : '' }}
                                                            </x-success-button>
                                                        @endif
                                                    @endif
                                                @endif

                                                @if ($item->parent_id)
                                                    @if (count($item->inventories) == 1 && $item->inventories[0]->stock > $item->parent()->first()->units_per_package)
                                                        <x-danger-button class="ml-3"
                                                            wire:click="package('{{ $item->code }}')">
                                                            <i class="fa fa-box mr-2"></i> Devolver a su empaque
                                                        </x-danger-button>
                                                    @endif
                                                @endif

                                            </div>
                                        </x-td>
                                    </x-tr>
                                @endforeach
                            @else
                                <x-tr>
                                    <x-td colspan="5">{{ __('No inventory items found.') }}</x-td>
                                </x-tr>
                            @endif
                        </x-slot>
                    </x-table>
                    <div class="mt-4">
                        @if ($inventoryItems->count() > 0)
                            {{ $inventoryItems->links() }}
                        @endif
                    </div>
                </x-spacing>
            </x-card>
        </div>
        @if ($product_id != null)
            <div class="w-full lg:w-96">
                <x-card>
                    <x-spacing>
                        <form wire:submit.prevent="store">
                            <div>
                                <x-label for="product_name">{{ __('Product') }}</x-label>
                                <x-input type="text" wire:model="product_name" id="product_name" readonly />
                                <x-input-error for="product_name" />
                            </div>
                            <div class="mt-3">
                                <x-label for="location">{{ __('Location') }}</x-label>
                                <x-input type="text" wire:model="location" id="location" />
                                <x-input-error for="location" />
                            </div>
                            <div class="mt-3">
                                <x-label for="stock">{{ __('Stock') }}</x-label>
                                <x-input type="number" wire:model="stock" id="stock" />
                                <x-input-error for="stock" />
                            </div>
                            <div class="mt-3">
                                <x-label for="minimum_stock">{{ __('Minimum Stock') }}</x-label>
                                <x-input type="number" wire:model="minimum_stock" id="minimum_stock" />
                                <x-input-error for="minimum_stock" />
                            </div>
                            <div class="mt-3">
                                <x-label for="expiry_date">{{ __('Expiry Date') }}</x-label>
                                <x-input type="date" wire:model="expiry_date" id="expiry_date" />
                                <x-input-error for="expiry_date" />
                            </div>
                            <div class="text-right mt-4">
                                <x-secondary-button type="button" wire:click="closeForm"
                                    class="mr-2">{{ __('Cancel') }}</x-secondary-button>
                                <x-button type="submit" wire:click="store"
                                    class="ml-3">{{ __('Save') }}</x-button>
                            </div>
                        </form>
                    </x-spacing>
                </x-card>
            </div>
        @endif
    </div>





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
