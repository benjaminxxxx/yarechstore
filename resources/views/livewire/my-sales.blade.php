<div>
    <x-card>
        <x-spacing>
            <x-h2 class="mb-2">
                Ventas Realizadas
            </x-h2>
            <x-table>
                <x-slot name="thead">
                    <tr>
                        <x-th value="N°" class="text-center" />
                        <x-th value="Emisión" class="text-center" />
                        <x-th value="Fecha de pago" class="text-center" />
                        <x-th value="Cliente" />
                        <x-th value="Número" />
                        <x-th value="Items" class="text-center" />
                        <x-th value="Estado" class="text-center" />
                        <x-th value="Gravado" class="text-right" />
                        <x-th value="IGV" class="text-right" />
                        <x-th value="Saldo" class="text-right" />
                        <x-th value="DOCUMENTOS" class="text-center" />
                    </tr>
                </x-slot>
                <x-slot name="tbody">
                    @if ($sales && $sales->count())
                        @foreach ($sales as $index => $sale)
                            <x-tr>
                                <x-th value="{{ $index + 1 }}" class="text-center" />
                                <x-th value="{{ $sale->emision_date }}" class="text-center" />
                                <x-th value="{{ $sale->pay_date }}" class="text-center" />
                                <x-th value="{{ $sale->client }}" />
                                <x-th value="{{ $sale->sale_name }}" />
                                <x-th value="{{ $sale->total_items }}" class="text-center" />
                                <x-th class="text-center">
                                    @php
                                        $aditionalClass = '';
                                        $statusSpanish = '';
                                        switch ($sale->status) {
                                            case 'paid':
                                                $aditionalClass = 'bg-green-100 text-green-800';
                                                $statusSpanish = 'Pagado';
                                                break;
                                            case 'canceled':
                                                $aditionalClass = 'bg-red-100 text-red-800';
                                                $statusSpanish = 'Cancelado';
                                                break;
                                            case 'debt':
                                                $aditionalClass = 'bg-yellow-100 text-yellow-800';
                                                $statusSpanish = 'Por pagar';
                                                break;
                                            default:
                                                $aditionalClass = 'bg-red-100 text-red-800';
                                                $statusSpanish = $sale->status;
                                                break;
                                        }
                                    @endphp
                                    <x-badge class="{{ $aditionalClass }}">
                                        {{ $statusSpanish }}
                                    </x-badge>
                                </x-th>
                                <x-th value="{{ $sale->total_amount }}" class="text-right" />
                                <x-th value="{{ $sale->igv }}" class="text-right" />
                                <x-th value="{{ $sale->saldo }}" class="text-right" />
                                <x-td class="text-center">
                                    <div class="flex items-center gap-3 justify-end">
                                        @if ($sale->signed_xml_path)
                                            <x-success-button
                                                wire:click.prevent="downloadXML('{{ $sale->signed_xml_path }}')">
                                                XML
                                            </x-success-button>
                                        @endif
                                        @if ($sale->cdr_path)
                                            <x-success-button
                                                wire:click.prevent="downloadCDR('{{ $sale->cdr_path }}')">
                                                CDR
                                            </x-success-button>
                                        @endif
                                        @if ($sale->document_path)
                                            <x-success-button
                                                wire:click.prevent="downloadDocument('{{ $sale->document_path }}')">
                                                Voucher
                                            </x-success-button>
                                        @endif
                                        @if ($sale->document_path_oficial)
                                            <x-success-button
                                                wire:click.prevent="downloadDocument('{{ $sale->document_path_oficial }}')">
                                                Voucher A4
                                            </x-success-button>
                                        @endif

                                        <div x-data="{ open: $wire.entangle('showDropdown') }">
                                            <x-button x-on:click="open = true">Mas Opciones</x-button>
                                            <div class="relative" x-show="open" x-on:click.outside="open = false">
                                                <ul class="absolute bg-white shadow-lg w-auto right-0">
                                                    <li><a href="#"
                                                            wire:click.prevent="openDetailOption({{ $sale->id }})"
                                                            class="whitespace-nowrap text-left px-4 py-3 block hover:bg-gray-200 w-full">Ver
                                                            Detalle</a></li>

                                                    <li><a href="#"
                                                            wire:click="generateNewVoucher({{ $sale->id }})"
                                                            class="whitespace-nowrap text-left px-4 py-3 block hover:bg-gray-200 w-full">Volver a generar el Voucher</a></li>
                                                </ul>
                                            </div>

                                        </div>
                                    </div>

                                </x-td>
                            </x-tr>
                        @endforeach
                    @else
                        <x-tr>
                            <x-td colspan="4">Aún no hay ventas</x-td>
                        </x-tr>
                    @endif
                </x-slot>
            </x-table>
            <div class="my-5">
                @if ($sales && $sales->count())
                    {{ $sales->links() }}
                @endif
            </div>
        </x-spacing>
    </x-card>
</div>
