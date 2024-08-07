<div>
    <x-card>
        <x-spacing>
            <div class="mb-2 md:mb-4 flex items-center">
                <x-button wire:click="openForm()">{{ __('Add New Product') }}</x-button>
                <form class="shadow-lg ml-3">
                    <label for="default-search"
                        class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>
                    <div class="relative">
                        <div
                            class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none text-indigo-600">
                            <i class="fa fa-search"></i>
                        </div>
                        <x-input type="search" wire:model="search" wire:keyup="searching" id="default-search"
                            class="pl-10" autocomplete="off" placeholder="{{ __('Search...') }}" required />
                    </div>
                </form>
            </div>
        </x-spacing>
    </x-card>
    <x-card class="mt-5">
        <x-spacing>
            <div class="grid grid-cols-12 gap-4">
                @if ($products && $products->count() > 0)
                    @foreach ($products as $product)
                        <div
                            class="relative group bg-white col-span-12 md:col-span-4 lg:col-span-3 2xl:col-span-2 hover-show rounded-lg overflow-hidden shadow-md">
                            <!-- Tarjeta del producto -->
                            <x-card class="relative h-full">
                                <x-spacing class="text-center">
                                    <img src="{{ $product->photo_url }}" class="w-auto m-auto" alt="" />
                                    <p class="py-2"><b>{{ $product->name }}</b></p>
                                    <p><b class="text-indigo-600">S/ {{ $product->final_price }}</b></p>
                                    <div class="flex lg:hidden items-center justify-center gap-5 mt-3">
                                        <x-button wire:click="see({{ $product->id }})">
                                            <i class="fa fa-eye mr-2"></i>
                                            Ver
                                        </x-button>
                                        <x-button wire:click="duplicate({{ $product->id }})">
                                            <i class="fa fa-copy mr-2"></i>
                                            Duplicar
                                        </x-button>
                                    </div>
                                </x-spacing>
                            </x-card>

                            <!-- Contenedor de los botones, que aparece en hover -->
                            <div
                                class="lg:absolute lg:inset-0 items-center justify-center hidden lg:flex gap-2 lg:bg-opacity-20 lg:bg-black p-4 hover-options">
                                <x-option-button wire:click="see({{ $product->id }})">
                                    <i class="fa fa-eye mr-2"></i>
                                    Ver
                                </x-option-button>
                                <x-option-button wire:click="duplicate({{ $product->id }})">
                                    <i class="fa fa-copy mr-2"></i>
                                    Duplicar
                                </x-option-button>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </x-spacing>
    </x-card>
    <!-- Modal -->
    <x-dialog-modal-header wire:model="isProductOpen" maxWidth="full">
        <x-slot name="title">
            <div class="flex items-center justify-between">
                <div class="">
                    MI PRODUCTO
                </div>
                <div class="flex-shrink-0">
                    <button wire:click="closeForm" class="focus:outline-none">
                        <i class="fa-solid fa-circle-xmark"></i>
                    </button>
                </div>
            </div>
        </x-slot>
        <x-slot name="content">
            <div class="grid grid-cols-3 gap-3">
                <div class="col-span-3 lg:col-span-2">
                    <div class="grid grid-cols-2 gap-5">
                        <div class="col-span-2 lg:col-span-1">
                            <x-label for="name">Nombre del producto</x-label>
                            <x-input type="text" wire:model="name" id="name" />
                            <x-input-error for="name" />
                        </div>
                        <div class="col-span-2 lg:col-span-1">
                            <x-label for="unit_type">Unidad de medida</x-label>
                            <x-select wire:model="unit_type" id="unit_type">
                                @if ($units)
                                    @foreach ($units as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                    @endforeach
                                @endif
                            </x-select>
                            <x-input-error for="unit_type" />
                        </div>
                        <div class="col-span-2 lg:col-span-1">
                            <x-label for="purchase_price">Precio de compra</x-label>
                            <x-input type="number" wire:model="purchase_price" id="purchase_price" />
                            <x-input-error for="purchase_price" />
                        </div>
                        <div class="col-span-2 lg:col-span-1">
                            <x-label for="units_per_package">Unidades por envase</x-label>
                            <x-input type="number" wire:model="units_per_package" id="units_per_package" />
                            <x-input-error for="units_per_package" />
                        </div>
                        <div class="col-span-2">
                            <div class="flex w-full justify-between mb-4">
                                <div>
                                    <x-label for="base_price">Precio base</x-label>
                                    <x-input type="number" wire:model="base_price" wire:keyup="calculateFinalPrice"
                                        id="base_price" />
                                    <x-input-error for="base_price" />
                                </div>
                                <div class=" pt-8 h-full">
                                    <b class="text-indigo-600 text-2xl">+</b>
                                </div>
                                <div>
                                    <x-label for="tax_id">Impuestos</x-label>
                                    <x-select wire:model="tax_id" id="tax_id" wire:change="calculateFinalPrice">
                                        <option value="1">IGV (18%)</option>
                                        <option value="2">Exonerado (0.00%)</option>
                                    </x-select>
                                </div>
                                <div class=" pt-8 h-full">
                                    <b class="text-indigo-600 text-2xl">=</b>
                                </div>
                                <div>
                                    <x-label for="final_price">Precio final</x-label>
                                    <x-input type="number" wire:model="final_price" wire:keyup="calculateBasePrice"
                                        id="final_price" />
                                    <x-input-error for="final_price" />
                                </div>
                            </div>
                        </div>
                        <div class="col-span-2 lg:col-span-1">
                            <x-label for="brand_id">Marca</x-label>
                            <x-select wire:model="brand_id" id="brand_id">
                                <option value="">Sin Marca</option>
                                @if ($brands)
                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                    @endforeach
                                @endif
                            </x-select>
                            <x-input-error for="brand_id" />
                        </div>
                        <div class="col-span-2 lg:col-span-1">
                            <x-label for="supplier_id">Proveedor</x-label>
                            <x-select wire:model="supplier_id" id="supplier_id">
                                <option value="">Sin proveedor</option>
                                @if ($suppliers)
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                    @endforeach
                                @endif
                            </x-select>
                            <x-input-error for="supplier_id" />
                        </div>
                        <div class="col-span-2">
                            <x-label for="description">Descripción</x-label>
                            <x-textarea rows="3" id="description" wire:model="description"></x-textarea>
                            <x-input-error for="description" />
                        </div>
                        <div class="col-span-2 lg:col-span-1">
                            <x-label>Categorías</x-label>
                            <x-input type="text" wire:click="openCategories" autocomplete="off" class="mb-2" />

                            @if ($productCategories)
                                @foreach ($productCategories as $categoryId)
                                    <span id="badge-dismiss-default"
                                        class="inline-flex items-center px-3 py-2 mb-1 me-2 text-sm font-medium text-indigo-800 bg-indigo-100 rounded dark:bg-indigo-900 dark:text-indigo-300">
                                        {{ $categoriesFull[$categoryId]->name }}
                                        <button type="button"
                                            class="inline-flex items-center p-1 ms-2 text-sm text-indigo-400 bg-transparent rounded-sm hover:bg-indigo-200 hover:text-indigo-900 dark:hover:bg-indigo-800 dark:hover:text-indigo-300"
                                            wire:click="removeCategory({{ $categoryId }})">
                                            <i class="fa fa-remove"></i>
                                            <span class="sr-only">Remove badge</span>
                                        </button>
                                    </span>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-span-3 lg:col-span-1">
                    <div>
                        @if ($image_path || $generic_image_url)
                            @if ($image_path != null)
                                <!-- Cuando la imagen ya está guardada -->
                                <img src="{{ asset($image_path) }}" class="w-full object-cover">
                            @endif
                            @if ($generic_image_url != null)
                                <!-- Cuando se está previsualizando una imagen temporal -->
                                <img src="{{ $generic_image_url->temporaryUrl() }}" class="w-full object-cover">
                            @endif
                            <x-option-button type="button" class="w-full mt-2" wire:click="deleteImage">Eliminar
                                imagen</x-option-button>
                        @else
                            <div wire:loading wire:target="generic_image_url">Subiendo...</div>
                            <div class="flex items-center justify-center w-full">
                                <label for="dropzone-file"
                                    class="flex flex-col items-center justify-center w-full h-36 border-2 border-slate-300 border-dashed rounded-lg cursor-pointer bg-slate-100 dark:hover:bg-gray-800 dark:bg-gray-700 hover:bg-slate-200 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                            <path stroke="currentColor" stroke-linecap="round"
                                                stroke-linejoin="round" stroke-width="2"
                                                d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                                        </svg>
                                        <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span
                                                class="font-semibold">Clic para subir imagen</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 text-center">PNG, JPG or GIF
                                            (MAX.
                                            800x400px)</p>
                                    </div>
                                    <input id="dropzone-file" type="file" wire:model="generic_image_url"
                                        class="hidden" />
                                </label>
                            </div>
                        @endif
                        <x-input-error for="generic_image_url" />
                    </div>
                    <div class="mb-1 mt-3">
                        <x-label for="barcode">Código de barra</x-label>
                        <x-input type="text" wire:model="barcode" id="barcode" />
                        <x-input-error for="barcode" />
                    </div>
                    <div class="mb-1">
                        <x-label for="sunatcode">Código de SUNAT</x-label>
                        <x-input type="text" wire:model="sunatcode" id="sunatcode" />
                        <x-input-error for="sunatcode" />
                    </div>
                    @if ($productId)

                        @if (!$product_child)
                            <div class="my-3 w-full">
                                <x-success-button type="button" class="w-full"
                                    wire:click="createProductChild({{ $productId }})">Crear producto
                                    hijo</x-success-button>
                            </div>
                        @endif
                    @endif
                </div>
                @if ($product_child)
                    <div class="col-span-3">
                        <x-table>
                            <x-slot name="thead">
                                <tr>
                                    <x-th value="{{ __('Product Image') }}" />
                                    <x-th value="{{ __('Product Name') }}" />
                                    <x-th value="{{ __('Purchase price') }}" />
                                    <x-th value="{{ __('Unit type') }}" />
                                    <x-th value="{{ __('Final price') }}" />
                                    <x-th value="{{ __('Barcode') }}" />
                                    <x-th value="{{ __('Sunar code') }}" />
                                    <x-th value="{{ __('Actions') }}" class="text-center" />
                                </tr>
                            </x-slot>
                            <x-slot name="tbody">
                                <x-tr>
                                    <x-td>
                                        <img src="{{ $product_child->photo_url }}" alt="{{ $product_child->name }}"
                                            class="w-10 h-10 object-cover">
                                    </x-td>
                                    <x-td>{{ $product_child->name }}</x-td>
                                    <x-td>{{ $product_child->purchase_price }}</x-td>
                                    <x-td>{{ $product_child->unit->name }}</x-td>
                                    <x-td>{{ $product_child->final_price }}</x-td>
                                    <x-td>{{ $product_child->barcode }}</x-td>
                                    <x-td>{{ $product_child->sunatcode }}</x-td>
                                    <x-td class="text-center">
                                        <div class="flex items-center justify-center">
                                            <x-button wire:click="see({{ $product_child->id }})">
                                                <i class="fa fa-eye  mr-2"></i> {{ __('See') }}
                                            </x-button>
                                            <x-danger-button
                                                wire:confirm="{{ __('Are you sure you want to delete this item?') }}"
                                                wire:click="delete('{{ $product_child->code }}')" class="ml-1">
                                                <i class="fa fa-remove mr-2"></i> {{ __('Delete') }}
                                            </x-danger-button>

                                        </div>
                                    </x-td>
                                </x-tr>
                            </x-slot>
                        </x-table>
                    </div>
                @endif
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-button-normal type="button" wire:click="closeForm" class="mr-2">Cancelar</x-button-normal>
            @if ($product_child)
            <x-button type="button" wire:click="save">Guardar y actualizar producto hijo</x-button>
            @else
            <x-button type="button" wire:click="save">Guardar</x-button>
            @endif
        </x-slot>
    </x-dialog-modal-header>
    <x-dialog-modal-header wire:model="isCategoryOpen" maxWidth="full">
        <x-slot name="title">
            <div class="flex items-center justify-between">
                <div class="">
                    Categorías
                </div>
                <div class="flex-shrink-0">
                    <button wire:click="$set('isCategoryOpen', false)" class="focus:outline-none">
                        <i class="fa-solid fa-circle-xmark"></i>
                    </button>
                </div>
            </div>
        </x-slot>
        <x-slot name="content">
            <div class="grid grid-cols-4 gap-2">
                @foreach ($categories as $category)
                    <div class="category col-span-4 md:col-span-2 lg:col-span-1">
                        <div class="flex items-center mt-4 mb-2">
                            <x-checkbox id="category_{{ $category->id }}" wire:model="productCategories"
                                type="checkbox" value="{{ $category->id }}" />
                            <x-label class="!font-bold ml-2 !mb-0" for="category_{{ $category->id }}">
                                {{ $category->name }}</x-label>
                        </div>
                        @if ($category->children->isNotEmpty())
                            <div class="ml-4">
                                @foreach ($category->children as $child)
                                    <div class="flex items-center mb-2 subcategory">
                                        <x-checkbox id="subcategory_{{ $child->id }}"
                                            wire:model="productCategories" type="checkbox"
                                            value="{{ $child->id }}" />
                                        <x-label for="subcategory_{{ $child->id }}" class="ml-2  !mb-0">
                                            {{ $child->name }}</x-label>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

        </x-slot>
        <x-slot name="footer">
            <x-button type="button" wire:click="$set('isCategoryOpen', false)">Aceptar</x-button>
        </x-slot>
    </x-dialog-modal-header>
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
