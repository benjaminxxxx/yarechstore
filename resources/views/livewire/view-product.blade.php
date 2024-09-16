<div>
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

            <div x-data="{ activeTab: 'inicio' }">
                <div class="bg-gray-100 p-2 lg:px-10 py-4">
                    <div class="overflow-x-auto snap-x snap-mandatory">
                        <ul class="flex space-x-4">
                            <li class="snap-start">
                                <a href="#" @click.prevent="activeTab = 'inicio'"
                                    class="text-gray-800 hover:text-orange-600 text-xs cursor-pointer"
                                    :class="{ 'text-orange-600': activeTab === 'inicio' }">General</a>
                            </li>
                            <li class="snap-start">
                                <a href="#" @click.prevent="activeTab = 'presentacion'"
                                    class="text-gray-800 hover:text-orange-600 text-xs cursor-pointer"
                                    :class="{ 'text-orange-600': activeTab === 'presentacion' }">Presentación</a>
                            </li>
                            <li class="snap-start">
                                <a href="#" @click.prevent="activeTab = 'stock'"
                                    class="text-gray-800 hover:text-orange-600 text-xs cursor-pointer"
                                    :class="{ 'text-orange-600': activeTab === 'stock' }">Stock</a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div x-show="activeTab === 'inicio'" class="mt-4">
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
                                            <x-input type="number" wire:model="base_price"
                                                wire:keyup="calculateFinalPrice" id="base_price" />
                                            <x-input-error for="base_price" />
                                        </div>
                                        <div class=" pt-8 h-full">
                                            <b class="text-secondary text-2xl">+</b>
                                        </div>
                                        <div>
                                            <x-label for="tax_id">Impuestos</x-label>
                                            <x-select wire:model="tax_id" id="tax_id"
                                                wire:change="calculateFinalPrice">
                                                <option value="1">IGV (18%)</option>
                                                <option value="2">Exonerado (0.00%)</option>
                                            </x-select>
                                        </div>
                                        <div class=" pt-8 h-full">
                                            <b class="text-secondary text-2xl">=</b>
                                        </div>
                                        <div>
                                            <x-label for="final_price">Precio final</x-label>
                                            <x-input type="number" wire:model="final_price"
                                                wire:keyup="calculateBasePrice" id="final_price" />
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
                                    <x-input type="text" wire:click="openCategories" autocomplete="off"
                                        class="mb-2" />

                                    @if ($productCategories)
                                        @foreach ($productCategories as $categoryId)
                                            <span id="badge-dismiss-default"
                                                class="inline-flex items-center px-3 py-2 mb-1 me-2 text-sm font-medium text-secondary bg-stone-100 rounded dark:bg-primaryDark">
                                                {{ $categoriesFull[$categoryId]->name }}
                                                <button type="button"
                                                    class="inline-flex items-center p-1 ms-2 text-sm text-secondary bg-transparent rounded-sm hover:bg-primary hover:text-primaryText"
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
                                        <img src="{{ $generic_image_url->temporaryUrl() }}"
                                            class="w-full object-cover">
                                    @endif
                                    <x-option-button type="button" class="w-full mt-2"
                                        wire:click="deleteImage">Eliminar
                                        imagen</x-option-button>
                                @else
                                    <div wire:loading wire:target="generic_image_url">Subiendo...</div>
                                    <div class="flex items-center justify-center w-full">
                                        <label for="dropzone-file"
                                            class="flex flex-col items-center justify-center w-full h-36 border-2 border-slate-300 border-dashed rounded-lg cursor-pointer bg-slate-100 dark:hover:bg-gray-800 dark:bg-gray-700 hover:bg-slate-200 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600">
                                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                                <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400"
                                                    aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                    fill="none" viewBox="0 0 20 16">
                                                    <path stroke="currentColor" stroke-linecap="round"
                                                        stroke-linejoin="round" stroke-width="2"
                                                        d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                                                </svg>
                                                <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span
                                                        class="font-semibold">Clic para subir imagen</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 text-center">PNG,
                                                    JPG or GIF
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
                        </div>
                    </div>
                </div>
                <div x-show="activeTab === 'presentacion'" class="mt-4">
                    <x-table>
                        <x-slot name="thead">
                            <tr>
                                <x-th value="Código de Barra" />
                                <x-th value="Unidad" />
                                <x-th value="Descripción" />
                                <x-th value="Factor" />
                                <x-th value="Precio" />
                                <x-th value="" />
                            </tr>
                        </x-slot>
                        <x-slot name="tbody">
                            @if ($presentations)
                                @foreach ($presentations as $indice => $presentation)
                                    <x-tr>
                                        <x-td>
                                            <x-input type="text" wire:model="presentations.{{ $indice }}.barcode"/>
                                        </x-td>
                                        <x-td>
                                            <x-select type="number" wire:model="presentations.{{ $indice }}.unit">
                                                @if ($units)
                                                    @foreach ($units as $unit)
                                                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                                    @endforeach
                                                @endif
                                            </x-select>
                                        </x-td>
                                        <x-td>
                                            <x-input type="text" wire:model="presentations.{{ $indice }}.description"/>
                                        </x-td>
                                        <x-td>
                                            <x-input type="number" wire:model="presentations.{{ $indice }}.factor"/>
                                        </x-td>
                                        <x-td>
                                            <x-input type="number" wire:model="presentations.{{ $indice }}.price"/>
                                        </x-td>
                                        <x-td class="text-center">
                                            <div class="flex items-center justify-center">

                                                <x-danger-button
                                                    wire:click="deletePresentation({{ $indice }})"
                                                    class="ml-1">
                                                    <i class="fa fa-remove"></i>
                                                </x-danger-button>

                                            </div>
                                        </x-td>
                                    </x-tr>
                                @endforeach
                            @endif
                            <x-tr>
                                <x-td><a href="#" class="underline font-bold text-secondary" wire:click.prevent="addPresentation">Agregar Presentación</a></x-td>
                            </x-tr>
                        </x-slot>
                    </x-table>
                </div>
                <div x-show="activeTab === 'stock'" class="mt-4">
                    <x-table>
                        <x-slot name="thead">
                            <tr>
                                <x-th value="Sucursal" />
                                <x-th value="Stock" />
                                <x-th value="" />
                            </tr>
                        </x-slot>
                        <x-slot name="tbody">
                            @if ($branches)
                                @foreach ($branches as $index => $branch_store)
                                    <x-tr>
                                        <x-td>
                                            <p>{{ $branch_store->name }}</p>
                                            <small>{{ $branch_store->address }}</small>
                                        </x-td>
                                        <x-td>
                                            <x-input type="text" placeholder="Stock" wire:model="branchArray.{{ $branch_store->id }}"/>
                                        </x-td>
                                    </x-tr>
                                @endforeach
                            @endif
                        </x-slot>
                    </x-table>
                </div>
            </div>
        </x-slot>
        <x-slot name="footer">
            @if($productId!=null)
            <x-danger-button type="button" wire:click="askDeleteProduct" class="mr-2">Eliminar Producto</x-danger-button>
            @endif
            <x-button-normal type="button" wire:click="closeForm" class="mr-2">Cancelar</x-button-normal>
            <x-button type="button" wire:click="save">Guardar</x-button>
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
</div>
