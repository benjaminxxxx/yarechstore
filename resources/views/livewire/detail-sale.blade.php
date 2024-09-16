<div>
    <x-dialog-modal-header wire:model="isDetailOpen" maxWidth="full">
        <x-slot name="title">
            <div class="flex items-center justify-between">
                <div>
                    VENTA REALIZADA
                </div>
                <div class="flex-shrink-0">
                    <button wire:click="closeForm" class="focus:outline-none">
                        <i class="fa-solid fa-circle-xmark"></i>
                    </button>
                </div>
            </div>
        </x-slot>

        <!-- Content: Detalles de la venta -->
        <x-slot name="content">
            <!-- Tabla de Presentaciones del Producto -->
            <h3 class="font-bold text-lg mb-4">Detalles de Venta</h3>
            <x-table>
                <x-slot name="thead">
                    <tr>
                        <x-th value="N°" class="text-center" />
                        <x-th value="Descripción" class="text-left" />
                        <x-th value="Cantidad" class="text-center" />
                        <x-th value="Precio Unitario" class="text-right" />
                        <x-th value="Precio Total" class="text-right" />
                    </tr>
                </x-slot>
                <x-slot name="tbody">
                    @if ($details)
                        @foreach ($details as $index => $detail)
                            <x-tr>
                                <x-td class="text-center">
                                    {{ $index + 1 }}
                                </x-td>
                                <x-td class="text-left">
                                    {{ $detail->product_name }}
                                </x-td>

                                <x-td class="text-center">
                                    {{ $detail->quantity }}
                                </x-td>
                                <x-td class="text-right">
                                    S/. {{ $detail->product_price }}
                                </x-td>

                                <x-th class="text-right">
                                    S/. {{ $detail->total_price }}
                                </x-th>
                            </x-tr>
                        @endforeach
                        <x-tr>
                            <x-th class="text-center" colspan="3">
                            </x-th>

                            <x-th class="text-right">
                                SUB TOTAL
                            </x-th>
                            <x-th class="text-right">
                                S/. {{ $sale->subtotal }}
                            </x-th>
                        </x-tr>
                        <x-tr>
                            <x-th class="text-center" colspan="3">
                            </x-th>

                            <x-th class="text-right">
                                IGV
                            </x-th>
                            <x-th class="text-right">
                                S/. {{ $sale->igv }}
                            </x-th>
                        </x-tr>
                        <x-tr>
                            <x-th class="text-center" colspan="3">
                            </x-th>

                            <x-th class="text-right">
                                TOTAL
                            </x-th>
                            <x-th class="text-right">
                                S/. {{ $sale->total_amount }}
                            </x-th>
                        </x-tr>
                    @endif
                </x-slot>
            </x-table>

            <!-- Tabla de Métodos de Pago -->
            <h3 class="font-bold text-lg mt-6 mb-4">Métodos de Pago</h3>
            <x-table>
                <x-slot name="thead">
                    <tr>
                        <x-th value="N°" class="text-center" />
                        <x-th value="Método" class="text-left" />
                        <x-th value="Monto" class="text-right" />
                    </tr>
                </x-slot>
                <x-slot name="tbody">
                    @if ($paymentMethods)
                        @foreach ($paymentMethods as $indexMethod => $paymentMethod)
                            <x-tr>
                                <x-td class="text-center">
                                    {{ $indexMethod + 1 }}
                                </x-td>
                                <x-td class="text-left">
                                    {{ $paymentMethod->method_name }}
                                </x-td>
                                <x-th class="text-right">
                                    S/. {{ $paymentMethod->amount }}
                                </x-th>
                            </x-tr>
                        @endforeach
                    @endif
                </x-slot>
            </x-table>

            <!-- Tabla de Cliente -->
            <h3 class="font-bold text-lg mt-6 mb-4">Cliente</h3>
            <x-table>
                <x-slot name="thead">
                    <tr>
                        <x-th value="Nombre Completo" class="text-left" />
                        <x-th value="Teléfono" class="text-left" />
                        <x-th value="Correo Electrónico" class="text-left" />
                        <x-th value="Documento" class="text-left" />
                    </tr>
                </x-slot>
                <x-slot name="tbody">
                    @if ($sale && $sale->customer)
                        <x-tr>
                            <x-td class="text-left">
                                {{ $sale->customer->fullname }}
                            </x-td>
                            <x-td class="text-left">
                                {{ $sale->customer->phone ?? 'No disponible' }}
                            </x-td>
                            <x-td class="text-left">
                                {{ $sale->customer->email ?? 'No disponible' }}
                            </x-td>
                            <x-td class="text-left">
                                {{ $sale->customer->documentType->short_name }}:
                                {{ $sale->customer->document_number }}
                            </x-td>
                        </x-tr>
                    @else
                        <x-tr>
                            <x-td colspan="4" class="text-center">
                                No se encontraron datos del cliente.
                            </x-td>
                        </x-tr>
                    @endif
                </x-slot>
            </x-table>


            <!-- Enviar documentos por correo -->
           
            @if ($sale)
                @php
                    $sender = false;
                @endphp
                <form>
                    @if ($sale->signed_xml_path || $sale->cdr_path || $sale->document_path || $sale->document_path_oficial)
                        @php
                            $sender = true;
                        @endphp
                         <h3 class="font-bold text-lg mt-6 mb-2">Enviar Documentos al Cliente</h3>
                        <p class="my-3">Selecciona los documentos a enviar</p>
                    @endif
                    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
                        <!-- Checkbox para XML -->
                        @if ($sale->signed_xml_path)
                            <label class="flex items-center">
                                <input type="checkbox" wire:model="selectedDocuments.signed_xml"
                                    value="{{ $sale->signed_xml_path }}" class="mr-2">
                                <span>XML</span>
                            </label>
                        @endif

                        <!-- Checkbox para CDR -->
                        @if ($sale->cdr_path)
                            <label class="flex items-center">
                                <input type="checkbox" wire:model="selectedDocuments.cdr" value="{{ $sale->cdr_path }}"
                                    class="mr-2">
                                <span>CDR</span>
                            </label>
                        @endif

                        <!-- Checkbox para Voucher -->
                        @if ($sale->document_path)
                            <label class="flex items-center">
                                <input type="checkbox" wire:model="selectedDocuments.voucher"
                                    value="{{ $sale->document_path }}" class="mr-2">
                                <span>Voucher</span>
                            </label>
                        @endif

                        <!-- Checkbox para Voucher A4 -->
                        @if ($sale->document_path_oficial)
                            <label class="flex items-center">
                                <input type="checkbox" wire:model="selectedDocuments.voucher_a4"
                                    value="{{ $sale->document_path_oficial }}" class="mr-2">
                                <span>Voucher A4</span>
                            </label>
                        @endif
                    </div>
                    @if ($sender)
                        <div class="flex flex-col md:flex-row items-center md:space-x-4 space-y-4 md:space-y-0 my-4">
                            <!-- Campo para correo del cliente -->
                            <div class="w-full md:w-2/3">
                                <x-input type="email" wire:model="customerEmail" placeholder="Correo del cliente"
                                    class="w-full" />
                                    <x-input-error for="customerEmail" />
                            </div>

                            <!-- Botón para enviar documentos -->
                            <div class="w-full md:w-1/3">
                                <x-button type="button" wire:click="sendDocuments"
                                    class="w-full justify-center">Enviar Documentos</x-button>
                            </div>
                        </div>
                    @endif
                </form>
            @endif
        </x-slot>
        
        <!-- Footer: Opciones -->
        <x-slot name="footer">
            <x-button-normal type="button" wire:click="closeForm" class="mr-2">Cerrar</x-button-normal>
        </x-slot>
    </x-dialog-modal-header>
    <x-loading wire:loading wire:target="sendDocuments" />
</div>
