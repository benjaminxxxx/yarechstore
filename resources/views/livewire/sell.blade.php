<div class="h-full">
    <div class="2xl:flex h-full p-3 bg-gray-50">
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
        <div class="flex-1 p-4 mb-5 pretty-scroll overflow-y-auto">
            @if ($branchCode)
                <div class="mb-3 md:flex items-center gap-5">
                    <x-button wire:loading.attr="disabled" wire:click="addCart">
                        Agregar otra Venta
                    </x-button>

                    <livewire:cart-close-cash-register />
                </div>

                <div class="relative">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none text-primary">
                        <i class="fa fa-search"></i>
                    </div>
                    <input type="search" wire:model.live="searchProduct"
                        class="block w-full p-3 bg-white ps-10 text-sm text-gray-800 border-0 rounded-2xl focus:ring-primary"
                        placeholder="Busca tus productos aquí" required />

                </div>
            @endif

            <div class=" py-5 pr-3 ">
                <div class="grid grid-cols-12 gap-10">
                    @if ($resultsProduct && $resultsProduct->count() > 0)
                        @foreach ($resultsProduct as $item)
                            @if ($item->presentations && $item->presentations->count() > 0)
                                <!-- Mostrar presentaciones disponibles -->
                                <x-product-card-presentation :photoUrl="$item->photo_url" :name="$item->name" :finalPrice="$item->final_price"
                                    :presentations="$item->presentations" :code="$item->code" />
                            @else
                                <x-product-card wire:click="addToCart('{{ $item->code }}')" :photoUrl="$item->photo_url"
                                    :name="$item->name" :finalPrice="$item->final_price" />
                            @endif
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

        <!-- Cart Sidebar -->
        <x-card class="w-full xl:w-80 2xl:w-[30rem] h-full bg-white flex flex-col justify-between">
            <x-spacing>
                <div class="pedidos flex items-center gap-3 mb-3">

                    @if ($showNextButton)
                        <x-button class="py-4" wire:click="nextSale">
                            <i class="fa fa-chevron-left"></i>
                        </x-button>
                    @endif
                    <div class="bg-primary w-full text-white py-3 px-4 shadow-lg rounded-lg font-bold">
                        Compra N°{{ $currentSale->id ?? 'N/A' }}
                    </div>
                    @if ($showPrevButton)
                        <x-button class="py-4" wire:click="prevSale">
                            <i class="fa fa-chevron-right"></i>
                        </x-button>
                    @endif

                </div>
                @if ($currentSale)
                    @if ($currentSale->status == 'cart')
                        <livewire:cart-client :saleCode="$currentSale->code" wire:key="client-sale-{{ $currentSale->code }}" />
                    @endif
                @endif

                <div class="relative overflow-auto">
                    <table class="w-full text-md text-left">
                        <thead>
                            <tr>
                                <th scope="col">
                                    <x-h5>Items</x-h5>
                                </th>
                                <th scope="col" class="text-center">
                                    <x-h5>Cnt</x-h5>
                                </th>
                                <th scope="col" class="text-right">
                                    <x-h5>Precio</x-h5>
                                </th>
                                <th scope="col">
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($currentSale)
                                @if ($currentSale->items)

                                    @if ($currentSale->status != 'cart')
                                        @foreach ($currentSale->items as $item)
                                            <tr>
                                                <th scope="row" class="py-2">
                                                    <p class="text-md font-semibold text-gray-900 dark:text-white">
                                                        {{ $item->product_name }}
                                                    </p>
                                                    <small class="font-semibold text-gray-600">
                                                        S/. {{ $item->product_price }}
                                                    </small>
                                                </th>
                                                <td class="px-1 py-4 text-center">
                                                    <p class="text-md font-semibold text-gray-900 dark:text-white">
                                                        {{ $item->quantity }}
                                                    </p>
                                                </td>
                                                <td class="px-1 py-4 text-right whitespace-nowrap">
                                                    <b>S/. {{ $item->total_price }}</b>
                                                </td>
                                                <td class="px-1 py-4 text-right">

                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        @foreach ($currentSale->items as $item)
                                            <tr>
                                                <th scope="row" class="py-2">
                                                    <p class="text-md font-semibold text-gray-900 dark:text-white">
                                                        {{ $item->product_name }}
                                                    </p>
                                                    <small class="font-semibold text-gray-600">
                                                        S/. {{ $item->product_price }}
                                                    </small>
                                                </th>
                                                <td class="px-1 py-4 text-center">
                                                    <div class="relative flex items-center justify-center">
                                                        <x-counter-button class="rounded-s text-sm"
                                                            wire:click.prevent="removeQuantityToCart({{ $item->id }})">
                                                            <i class="fa fa-minus"></i>
                                                        </x-counter-button>
                                                        <x-counter-input type="number"
                                                            wire:model.live="quantities.{{ $item->id }}"
                                                            wire:input="updateQuantityToCart({{ $item->id }})"
                                                            autocomplete="off" />
                                                        <x-counter-button class="rounded-e text-sm"
                                                            wire:click.prevent="addQuantityToCart({{ $item->id }})">
                                                            <i class="fa fa-plus"></i>
                                                        </x-counter-button>
                                                    </div>
                                                </td>
                                                <td class="px-1 py-4 text-right whitespace-nowrap"
                                                    x-data="{ hover: false }" @mouseenter="hover = true"
                                                    @mouseleave="hover = false">
                                                    <div>
                                                        <b x-show="!hover" class="w-[5.7rem] block">S/.
                                                            {{ $item->total_price }}</b>
                                                        <a href="#" x-show="hover"
                                                            wire:click.prevent="$dispatch('editProductInCart', ['{{ $item->id }}'])"
                                                            class="font-medium text-gray-800 text-center dark:text-blue-500 hover:underline w-[5.7rem] block">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                                <td class="px-1 py-4 text-right">

                                                    <a href="#"
                                                        wire:click.prevent="removeToCart({{ $item->id }})"
                                                        class="font-medium text-red-600 dark:text-blue-500 hover:underline">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                @endif
                            @endif
                        </tbody>
                    </table>
                </div>

            </x-spacing>

            <div class="mt-auto">
                <x-spacing>

                    @if ($currentSale && ($currentSale->status == 'paid' || $currentSale->status == 'debt'))
                        @if ($currentSale->prepayments->count() > 0)
                            @foreach ($currentSale->prepayments as $prepayment)
                                <div class="flex items-center my-2 gap-2">
                                    
                                    @if ($prepayment->hasDocumentVoucher)
                                        <x-button-a target="_blank" href="{{ $prepayment->hasDocumentVoucher }}">
                                            Anticipo {{ $prepayment->related_doc_type }} -
                                            {{ $prepayment->related_doc_number }}
                                        </x-button-a>
                                    @endif
                                    @if ($prepayment->hasDocument)
                                        <x-button-a target="_blank" href="{{ $prepayment->hasDocument }}">
                                            Anticipo {{ $prepayment->related_doc_type }} -
                                            {{ $prepayment->related_doc_number }} A4
                                        </x-button-a>
                                    @endif
                                </div>
                            @endforeach
                        @endif
                        @if ($currentSale->document_path)
                            @php
                                $buttonLabel = '';

                                if ($currentSale->invoiceType) {
                                    if ($currentSale->invoiceType->code == '01') {
                                        $buttonLabel = 'Imprimir Factura';
                                    } elseif ($currentSale->invoiceType->code == '03') {
                                        $buttonLabel = 'Imprimir Boleta';
                                    }
                                } else {
                                    $buttonLabel = 'Imprimir Recibo';
                                }
                            @endphp

                            @if ($buttonLabel)
                                <x-button-a target="_blank"
                                    href="{{ asset('uploads/' . $currentSale->document_path) }}">
                                    {{ $buttonLabel }}
                                </x-button-a>

                                @php
                                    // Versiones anteriores
                                    $originalFilePath = 'uploads/' . $currentSale->document_path;
                                    $officialFilePath =
                                        'uploads/' . str_replace('.pdf', '_oficial.pdf', $currentSale->document_path);

                                    $newPathOficial = 'uploads/' . $currentSale->xml_path;
                                @endphp

                                @if (file_exists(public_path($officialFilePath)) || file_exists(public_path($newPathOficial)))

                                    @if (file_exists(public_path($newPathOficial)))
                                        <x-button-a target="_blank" href="{{ asset($newPathOficial) }}">
                                            {{ $buttonLabel }} A4
                                        </x-button-a>
                                    @elseif (file_exists(public_path($officialFilePath)))
                                        <x-button-a target="_blank" href="{{ asset($officialFilePath) }}">
                                            {{ $buttonLabel }} A4
                                        </x-button-a>
                                    @endif
                                @endif
                            @endif
                        @endif
                    @endif


                </x-spacing>
                <x-spacing class="bg-slate-200">
                    <table class="w-full">
                        <tfoot>
                            <tr>
                                <th>
                                    <x-label class="text-left">
                                        Sub total
                                    </x-label>
                                </th>
                                <th>
                                    <x-label class="text-right">
                                        S/.
                                        {{ $currentSale ? number_format($currentSale->subtotal, 2) : '0.00' }}
                                    </x-label>
                                </th>
                            </tr>
                            <tr>
                                <th>
                                    <x-label class="text-left">
                                        IGV
                                    </x-label>
                                </th>
                                <th>
                                    <x-label class="text-right">
                                        S/.
                                        {{ $currentSale ? number_format($currentSale->igv, 2) : '0.00' }}
                                    </x-label>
                                </th>
                            </tr>
                            <tr>
                                <th>
                                    <x-h5 class="text-left">
                                        Total
                                    </x-h5>
                                </th>
                                <th>
                                    <x-h5 class="text-right">
                                        S/. {{ $currentSale ? number_format($currentSale->total_amount, 2) : '0.00' }}
                                    </x-h5>
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                    <div class="md:flex justify-between mt-5">


                        @if ($currentSale)
                            @if ($currentSale->status == 'cart')
                                <livewire:pay-sale :saleCode="$currentSale->code" wire:key="pay-sale-{{ $currentSale->code }}" />
                                <x-danger-button class="dark:bg-slate-900" wire:loading.attr="disabled"
                                    wire:click="removeCart('{{ $currentSale->code }}')">
                                    Eliminar Carrito
                                </x-danger-button>
                            @endif
                            @if ($currentSale->status == 'paid')
                                <x-danger-button class="dark:bg-slate-900" wire:loading.attr="disabled"
                                    wire:click="confirmarAnularVenta('{{ $currentSale->code }}')">
                                    Anular Venta
                                </x-danger-button>
                            @endif
                            @if ($currentSale->status == 'canceled')
                                <x-label>
                                    Venta Anulada
                                </x-label>
                            @endif
                        @endif
                    </div>
                </x-spacing>
            </div>
        </x-card>
        <style>
            /* Estilo para Chrome, Safari, Edge, Opera */
            input[type="number"]::-webkit-inner-spin-button,
            input[type="number"]::-webkit-outer-spin-button {
                -webkit-appearance: none;
                margin: 0;
            }

            /* Estilo para Firefox */
            input[type="number"] {
                -moz-appearance: textfield;
            }
        </style>
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
    <livewire:cart-change-price-product />
</div>
