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
                                                x-ref="amountInput"
                                                x-bind:focus="$refs.input{{ $selectedMethod }}.focus()"
                                                x-on:focus="$refs.amountInput.select()" />
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
                                <x-radio id="document_selected-1" value="boleta" wire:model="document_selected" />
                                <x-label for="document_selected-1" class="ms-2 !mb-0" value="Boleta" />
                            </div>
                            <div class="flex items-center mb-4">
                                <x-radio id="document_selected-2" value="factura" wire:model="document_selected" />
                                <x-label for="document_selected-2" class="ms-2 !mb-0" value="Factura" />
                            </div>
                            <div class="flex items-center mb-4">
                                <x-radio id="document_selected-4" value="recibo" wire:model="document_selected" />
                                <x-label for="document_selected-4" class="ms-2 !mb-0" value="Recibo" />
                            </div>

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
