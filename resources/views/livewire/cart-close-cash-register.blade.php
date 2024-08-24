<div>
    @if($cashRegisterOpen)
    <x-button wire:loading.attr="disabled" wire:click="openModalCashRegister">
        Cerrar Caja
    </x-button>
    @endif
    <!-- Modal para Cierre de Caja -->
    <x-dialog-modal wire:model="isFormOpen" maxWidth="full">
        <x-slot name="title">
            Cierre de Caja
        </x-slot>

        <x-slot name="content">
            <div class="grid grid-cols-3 gap-5">
                <div class="col-span-3 md:col-span-2">
                    <div class="overflow-auto pretty-scroll" style="max-height: 60vh;">
                        <x-table>
                            <x-slot name="thead">
                                <tr>
                                    <x-th value="Documento" />
                                    <x-th class="text-right" value="Monto Total" />
                                    <x-th value="Estado" />
                                    <!--<x-th value="Acciones" class="text-center" />-->
                                </tr>
                            </x-slot>
                            <x-slot name="tbody">
                                @if($sales && $sales->count()>0)
                                @foreach ($sales as $sale)
                                    <x-tr>
                                        <x-th value="{{ $sale->sale_name }}" />
                                        <x-th class="text-right" value="{{ $sale->total_amount_format }}" />
                                        <x-th value="{{ $sale->status_format }}" />
                                        <!--<x-td class="text-center">
                                            <div class="flex items-center justify-center">
                                                <x-button>
                                                    <i class="fa fa-eye mr-2"></i> Ver Detalle
                                                </x-button>
                                            </div>
                                        </x-td>-->
                                    </x-tr>
                                @endforeach
                                @endif
                            </x-slot>
                        </x-table>
                    </div>
                    
                </div>
                <div class="col-span-3 md:col-span-1">
                    @if($cashRegisterOpen)
                    <div class="mb-4">
                        <x-label for="current_amount">Monto Inicial en Caja + Efectivo agregado</x-label>
                        <x-input type="text" id="current_amount" value="{{'S/. ' . number_format($initial_and_current_amount, 2, '.', ',')}}" readonly />
                    </div>
        
                    <div class="mb-4">
                        <x-label for="initial_amount">Total Inicial en Caja</x-label>
                        <x-input type="text" id="initial_amount" readonly value="{{'S/. ' . number_format($cashRegisterOpen->initial_amount , 2, '.', ',')}}" />
                    </div>
                    <div class="mb-4">
                        <x-label for="initial_amount">Total Vuelto Efectivo Entregado</x-label>
                        <x-input type="text" id="initial_amount" readonly value="{{'S/. ' . number_format($changes, 2, '.', ',')}}" />
                    </div>
                    <div class="mb-4">
                        <x-label for="total_sales">Total de Ventas Pagadas</x-label>
                        <x-input type="text" id="total_sales" readonly value="{{'S/. ' . number_format($salesAmount, 2, '.', ',')}}" />
                    </div>
                    <div class="mb-4">
                        <x-label for="difference">Diferencia</x-label>
                        <x-input type="text" id="difference" readonly value="{{'S/. ' . number_format($difference, 2, '.', ',')}}" />
                    </div>
        
                    <div class="mt-4">
                        <x-label for="sales_summary">Resumen de Ventas</x-label>
                        <ul id="sales_summary">
                            <li>Asegúrese de contar todo el dinero en la caja y verificar que coincida con el total de ventas registrado.</li>
                            <li>Para que la caja esté correctamente cuadrada, la diferencia entre el dinero en caja y el total registrado debe ser 0.</li>
                        </ul>
                    </div>
                    @endif
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button type="button" wire:click="closeModalCashRegister"
                class="mr-2">Cancelar</x-secondary-button>
            <x-button type="submit" wire:click="confirmCloseCashRegister" class="ml-3">Cerrar Caja</x-button>
        </x-slot>
    </x-dialog-modal>

</div>
