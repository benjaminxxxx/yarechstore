<div>
    <x-secondary-button wire:click="pay">
        Pagar
    </x-secondary-button>

    <x-dialog-modal-blank wire:model="isFormPayOpen" maxWidth="complete">
        <div class="flex h-screen bg-white text-black" style="max-height:90vh">
            <!-- Left Column -->
            <div class="lg:w-[24rem] flex flex-col">
                <div class="h-1/2 bg-slate-400 overflow-y-auto">
                    <!-- Métodos de pago -->
                    <div class="w-full">
                        @foreach ($methods as $id => $method)
                            <x-payment-method-button :icon="$method['icon']" :label="$method['label']"
                                wire:click="addMethod('{{ $id }}')" />
                        @endforeach
                    </div>
                </div>
                <div class="h-1/2 bg-slate-200">
                    @if ($currentSale)
                        @if ($currentSale->status == 'cart')
                            <div class="p-2 md:py-3 px-5">
                                <livewire:cart-client :saleCode="$currentSale->code"
                                    wire:key="client-pay-sale-{{ $currentSale->code }}" />
                            </div>
                            <div class="p-2 md:py-2 px-5">
                                <x-label value="Fecha de emisión" />
                                <div x-data x-init="flatpickr($refs.datepicker, {
                                    dateFormat: 'Y-m-d',
                                    maxDate: '{{ now()->format('Y-m-d') }}',
                                    minDate: '{{ now()->subDays(2)->format('Y-m-d') }}',
                                    disable: [
                                        function(date) {
                                            // Deshabilitar todas las fechas que no están en el rango
                                            return date > new Date() || date < new Date().fp_incr(-2);
                                        }
                                    ],
                                    onChange: function(selectedDates, dateStr, instance) {
                                        @this.set('fecha_emision', dateStr);
                                    }
                                });">
                                    <x-input type="text" x-ref="datepicker" value="{{$fecha_emision}}" class="form-input"
                                        placeholder="Seleccionar fecha" />
                                </div>
                            </div>
                            <div class="p-2 md:py-2 px-5">
                                @if ($document_selected == 'factura' || $document_selected == 'boleta')

                                    @php
                                        // Extraer los métodos con amount mayor a cero
                                        $paymentMethods = collect($methodsAdded)->filter(function ($method, $key) {
                                            return $method['amount'] > 0 && $key !== 'client';
                                        });

                                        // Revisar si hay método cliente con monto mayor a cero
                                        $hasClient =
                                            array_key_exists('client', $methodsAdded) &&
                                            $methodsAdded['client']['amount'] > 0;
                                        $hasOtherThanClient = $paymentMethods->count() > 0; // Verifica si hay otros métodos con monto
                                    @endphp

                                    @if (!$hasClient)
                                        {{-- Caso en que no hay método "client", emitir la factura completa --}}
                                        <x-label
                                            value="Se va a generar una factura electrónica por el monto de S/. {{ $currentSale->total_amount }} soles" />
                                    @else
                                        {{-- Caso en que solo se ha seleccionado "client" --}}
                                        @if ($hasClient && !$hasOtherThanClient)
                                            <x-label
                                                value="El monto ha sido cargado a la cuenta del cliente. ¿Desea emitir la factura ahora?" />

                                            <div class="block my-5">
                                                <label for="emitFactura" class="flex items-center">
                                                    <x-checkbox id="emitFactura" wire:model="emitFactura" />
                                                    <span class="ms-2 text-sm">Emitir {{$document_selected == 'factura'?'Factura':'Boleta'}}</span>
                                                </label>
                                            </div>
                                            <div class="block my-5">
                                                <label for="emitRecibo3" class="flex items-center">
                                                    <x-checkbox id="emitRecibo3" wire:model="emitRecibo" />
                                                    <span class="ms-2 text-sm">Emitir Recibo de Pago</span>
                                                </label>
                                            </div>
                                        @else
                                            {{-- Caso en que hay otros métodos de pago además de "client" --}}
                                            <x-label
                                                value="Hay un adelanto por S/. {{ $paymentMethods->sum('amount') }} soles. ¿Desea emitir una factura de anticipo?" />

                                            <div class="block my-5">
                                                <label for="emitFacturaAnticipo" class="flex items-center">
                                                    <x-checkbox id="emitFacturaAnticipo" wire:model="emitFacturaAnticipo" />
                                                    <span class="ms-2 text-sm">Emitir {{$document_selected == 'factura'?'Factura':'Boleta'}} de Anticipo</span>
                                                </label>
                                            </div>
                                            <div class="block my-5">
                                                <label for="emitRecibo2" class="flex items-center">
                                                    <x-checkbox id="emitRecibo2" wire:model="emitRecibo" />
                                                    <span class="ms-2 text-sm">Emitir Recibo de Pago</span>
                                                </label>
                                            </div>
                                        @endif
                                    @endif
                                @endif
                            </div>
                        @endif
                    @endif

                </div>
            </div>

            <!-- Right Column -->
            <div class="flex-1 flex flex-col bg-slate-100">
                <div class="bg-secondary  text-3xl text-center text-white">
                    <x-spacing>
                        Total: S/. <span class="subtotal">{{ $subtotal }}</span>
                    </x-spacing>

                </div>
                <div class="flex-1 overflow-y-auto flex flex-col justify-center">
                    <!-- Lista de métodos de pago seleccionados -->
                    <x-spacing>
                        <div class="space-y-2">
                            @if (is_array($methodsAdded) && count($methodsAdded) > 0)
                                @foreach ($methodsAdded as $id => $methodAdded)
                                    @php
                                        $classItem = 'bg-white';
                                        if ($selectedMethod == $id) {
                                            $classItem = 'bg-slate-200 selected-method';
                                        }

                                    @endphp

                                    <x-payment-method-item data-amount="{{ $methodAdded['amount'] }}"
                                        class="{{ $classItem }}" :id="$id" :icon="$methodAdded['icon']"
                                        :label="$methodAdded['label']" wire:click="selectMethod('{{ $id }}')">
                                        <div x-data class="flex items-center">
                                            <x-label class="mr-3">
                                                S/.
                                            </x-label>
                                            <x-input type="text"
                                                wire:model.live="methodsAdded.{{ $id }}.amount"
                                                x-ref="amountInput{{ $selectedMethod }}"
                                                x-bind:focus="$refs.amountInput{{ $selectedMethod }}.focus()"
                                                x-on:focus="$refs.amountInput{{ $selectedMethod }}.select()" />
                                            <button wire:click.stop="removeMethod('{{ $id }}')"
                                                class="text-red-600 ml-10">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </div>
                                    </x-payment-method-item>
                                @endforeach
                            @else
                                <div class="flex justify-center">
                                    <x-label>Por favor seleccione un metodo de pago</x-label>
                                </div>
                            @endif
                        </div>
                    </x-spacing>

                </div>
                <x-spacing>
                    @php
                        $classChange = 'text-green-600';
                        $changeText = 'Vuelto';
                        if ($change < 0) {
                            $classChange = 'text-red-600';
                            $changeText = 'Falta abonar';
                        }
                    @endphp
                    <div class="flex items-center justify-between ">
                        <label for="change" class="text-lg">{{ $changeText }}</label>
                        <div class="flex items-center gap-3 {{ $classChange }}">

                            <p class="font-semibold text-2xl">S/.</p> <span
                                class="text-right !text-2xl !font-semibold set-change ">{{ $change }}</span>
                        </div>
                    </div>
                    <div>
                        @if ($isCashRegisterEnabled)
                            <div class="flex">
                                <div class="flex items-center h-5">
                                    <x-checkbox id="no_register_change" wire:model="no_register_change" />

                                </div>
                                <div class="ms-2 text-sm">
                                    <x-label for="no_register_change">No registrar el vuelto en Caja</x-label>
                                    <p id="no_register_change-text"
                                        class="text-xs font-normal text-gray-500 dark:text-gray-300">Si el vuelto lo va
                                        a enviar al cliente mediante transferencia o billetera digital, debe activar
                                        esta opción</p>
                                </div>
                            </div>
                        @endif
                        <div class="mt-10">
                            <div class="flex items-center mb-4">
                                <x-radio id="document_selected-1" value="boleta" wire:model.live="document_selected" />
                                <x-label for="document_selected-1" class="ms-2 !mb-0" value="Boleta" />
                            </div>
                            <div class="flex items-center mb-4">
                                <x-radio id="document_selected-2" value="factura" wire:model.live="document_selected" />
                                <x-label for="document_selected-2" class="ms-2 !mb-0" value="Factura" />
                            </div>
                            <!--<div class="flex items-center mb-4">
                                <x-radio id="document_selected-4" value="recibo" wire:model.live="document_selected" />
                                <x-label for="document_selected-4" class="ms-2 !mb-0" value="Recibo" />
                            </div>-->

                        </div>
                    </div>
                </x-spacing>

                <div class="flex items-center">
                    <!-- Botones -->
                    @if ($isPaymentMethodAvailable)
                        <button wire:click="processSale"
                            class="w-1/2 bg-primary border-none hover:bg-primaryHoverOpacity text-white focus:outline-none text-2xl flex items-center justify-center">
                            <x-spacing>
                                <i class="fa fa-check mr-2"></i> Procesar Venta
                            </x-spacing>
                        </button>
                    @else
                        <button disabled
                            class="w-1/2 bg-primary border-none hover:bg-primaryHoverOpacity text-white focus:outline-none text-2xl flex items-center justify-center">
                            <x-spacing>
                                <i class="fa fa-check mr-2"></i> Agregar método de pago
                            </x-spacing>
                        </button>
                    @endif

                    <button wire:click="backStep"
                        class="w-1/2 bg-white hover:bg-stone-100 text-stone-600 bg-text-white text-3xl flex items-center justify-center">

                        <x-spacing>
                            <i class="fa fa-arrow-left mr-2"></i> Regresar
                        </x-spacing>
                    </button>
                </div>
            </div>
        </div>
    </x-dialog-modal-blank>
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


    <script>
        document.addEventListener('focus-input', () => {
            setTimeout(() => {
                const selectedInput = document.querySelector('.selected-method input');
                if (selectedInput) {
                    selectedInput.focus();
                    selectedInput.select(); // Selecciona el texto dentro del input
                }
            }, 100); // Retraso de 100 ms para asegurar que el DOM esté actualizado
        });
    </script>
</div>
