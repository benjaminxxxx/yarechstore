<div>
    <!--REGISTERXML-->
    <div class="flex items-center gap-5">
        <x-h3 class="my-5">
            Registro de venta mediante XML
        </x-h3>

        <div x-data="{
            handleFile(event) {
                const file = event.target.files[0];
                if (file) {
                    console.log('Archivo seleccionado:', file.name);
                    // Aquí puedes manejar el archivo, como validarlo o subirlo
                }
            }
        }">
            <x-button type="button" id="uploadButton" @click="$refs.xmlInput.click()">
                Subir XML
            </x-button>

            <input type="file" id="xmlInput" accept=".xml" x-ref="xmlInput" wire:model="xml_file" style="display: none"
                @change="handleFile">
        </div>
    </div>
    <x-card>
        <x-spacing>
            @if (!$thereAreCompanies)
                <p class="my-4">
                    Estimado proveedor, al subir su primer XML podremos registrar su empresa, si desea hacerlo antes,
                    visite el menu <a href="{{ route('supplier.companies') }}"
                        class="text-secondary underline">Compañias</a> para poder agregar las empresas de forma manual.
                </p>
            @endif
            <x-table>
                <x-slot name="thead">
                    <tr>
                        <x-th value="ID" class="text-center" />
                        <x-th value="Proveedor" />
                        <x-th value="Fecha de Compra" class="text-center" />
                        <x-th value="Número de Operación" class="text-center" />
                        <x-th value="Monto Total" class="text-right" />
                        <x-th value="Estado" class="text-center" />
                        <x-th value="Fecha de Creación" />
                        <x-th value="Acciones" class="text-center" />
                    </tr>
                </x-slot>
                <x-slot name="tbody">
                    @if ($purchases && $purchases->count())
                        @foreach ($purchases as $purchase)
                            <x-tr>
                                <x-td class="text-center">
                                    {{ $purchase->id }}
                                </x-td>
                                <x-td>
                                    {{ $purchase->supplier->name }}
                                </x-td>
                                <x-td class="text-center">
                                    {{ $purchase->purchase_date }}
                                </x-td>
                                <x-td class="text-center">
                                    {{ $purchase->invoice_code }}
                                </x-td>
                                <x-td class="text-right">
                                    {{ $purchase->total_amount }}
                                </x-td>
                                <x-td class="text-center">
                                    {{ $purchase->status }}
                                </x-td>
                                <x-td>
                                    {{ $purchase->created_at }}
                                </x-td>
                                <x-td class="text-center">
                                    <div class="flex items-center justify-center gap-3">
                                        <x-success-button wire:click.prevent="downloadXML('{{ $purchase->xml_file }}')">
                                            XML
                                        </x-success-button>

                                        <x-button wire:click="seeDetail({{ $purchase->id }})">
                                            Ver Detalle
                                        </x-button>

                                        <x-danger-button
                                            wire:confirm="¿Está seguro que desea eliminar esta compra? No podrá recuperar la información"
                                            wire:click="delete({{ $purchase->id }})">
                                            <i class="fa fa-trash"></i>
                                        </x-danger-button>
                                    </div>
                                </x-td>
                            </x-tr>
                        @endforeach
                    @else
                        <tr>
                            <x-td colspan="13" class="text-center">
                                No se encontraron compras.
                            </x-td>
                        </tr>
                    @endif
                </x-slot>
            </x-table>

        </x-spacing>
    </x-card>
    <x-dialog-modal-header wire:model="isFormOpen" maxWidth="full">
        <x-slot name="title">
            <div class="flex items-center justify-between">
                <div class="">
                    DETALLE DE COMPRA
                </div>
                <div class="flex-shrink-0">
                    <button wire:click="closeForm" class="focus:outline-none">
                        <i class="fa-solid fa-circle-xmark"></i>
                    </button>
                </div>
            </div>
        </x-slot>
        <x-slot name="content">

            <h3 class="font-bold text-lg mb-4">Detalles de Venta</h3>
            <x-table>
                <x-slot name="thead">
                    <tr>
                        <x-th value="N°" class="text-center" />
                        <x-th value="Producto" class="text-left" />
                        <x-th value="Identificación del Producto" class="text-left" />
                        <x-th value="Cantidad" class="text-center" />
                        <x-th value="Precio Unitario" class="text-right" />
                        <x-th value="Impuesto del Producto" class="text-right" />
                        <x-th value="Precio Total" class="text-right" />
                    </tr>
                </x-slot>
                <x-slot name="tbody">
                    @if ($purchase)
                        @if ($purchaseDetail)
                            @foreach ($purchaseDetail as $index => $detail)
                                <x-tr>
                                    <x-td class="text-center">
                                        {{ $index + 1 }}
                                    </x-td>
                                    <x-td class="text-left">
                                        {{ $detail->product_name }}
                                    </x-td>
                                    <x-td class="text-left">
                                        {{ $detail->product_identification }}
                                    </x-td>
                                    <x-td class="text-center">
                                        {{ $detail->quantity }}
                                    </x-td>
                                    <x-td class="text-right">
                                        S/. {{ $detail->price }}
                                    </x-td>
                                    <x-td class="text-right">
                                        S/. {{ $detail->product_tax_amount }}
                                    </x-td>
                                    <x-th class="text-right">
                                        S/. {{ $detail->product_price }}
                                    </x-th>
                                </x-tr>
                            @endforeach
                            <x-tr>
                                <x-th class="text-center" colspan="4">
                                </x-th>
                                <x-th class="text-right">
                                    SUB TOTAL
                                </x-th>
                                <x-th class="text-right" colspan="2">
                                    S/. {{ $purchase->sub_total }}
                                </x-th>
                            </x-tr>
                            <x-tr>
                                <x-th class="text-center" colspan="4">
                                </x-th>
                                <x-th class="text-right">
                                    IGV
                                </x-th>
                                <x-th class="text-right" colspan="2">
                                    S/. {{ $purchase->tax_amount }}
                                </x-th>
                            </x-tr>
                            <x-tr>
                                <x-th class="text-center" colspan="4">
                                </x-th>
                                <x-th class="text-right">
                                    TOTAL
                                </x-th>
                                <x-th class="text-right" colspan="2">
                                    S/. {{ $purchase->total_amount }}
                                </x-th>
                            </x-tr>
                        @endif
                    @endif
                </x-slot>
            </x-table>
        </x-slot>
        <x-slot name="footer">

            <x-button-normal type="button" wire:click="closeForm" class="mr-2">Cerrar</x-button-normal>
        </x-slot>
    </x-dialog-modal-header>
</div>
