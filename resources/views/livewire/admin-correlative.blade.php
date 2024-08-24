<div>
    <x-card>
        <x-spacing>
            <div class="mb-2 md:mb-4">
                <x-label for="branch" value='Selecciona la sucursal' />
                <x-select id="branch" wire:model.live="selectedBranch">
                    <option value="">Seleccione una sucursal</option>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </x-select>
            </div>

            <!-- Table to display correlatives -->
            @if ($selectedBranch)
                <x-table>
                    <x-slot name="thead">
                        <tr>
                            <x-th value="Tipo de Documento" />
                            <x-th value="Serie" />
                            <x-th value="Correlativo" />
                            <x-th value="Acciones" class="text-center" />
                        </tr>
                    </x-slot>
                    <x-slot name="tbody">
                        @if ($correlatives->count())
                            @foreach ($correlatives as $correlative)
                                <x-tr>
                                    <x-th>
                                        {{ $correlative->invoiceType->name }}
                                    </x-th>
                                    <x-th>
                                        {{ $correlative->series }}
                                    </x-th>
                                    <x-th>
                                        {{ $correlative->current_correlative }}
                                    </x-th>
                                    <x-td class="text-center">
                                        <div class="flex items-center justify-center">
                                            <x-danger-button wire:confirm="Está seguro que desea eliminar este correlativo?, no podra realizar operaciones que involucren este tipo de documentos" wire:click="delete({{$correlative->id}})">
                                                <i class="fa fa-remove mr-2"></i> Eliminar
                                            </x-danger-button>
                                        </div>
                                    </x-td>
                                </x-tr>
                            @endforeach
                        @endif

                        <x-tr>
                            <x-th>
                                <x-select id="branch" wire:model.live="selectedInvoiceType">
                                    <option value="">Seleccione una tipo de documento</option>
                                    @foreach ($invoiceTypes as $invoiceType)
                                        <option value="{{ $invoiceType->id }}">{{ $invoiceType->name }}</option>
                                    @endforeach
                                </x-select>
                            </x-th>
                            <x-th>
                                <x-input type="text" wire:model="serie" class="uppercase" />
                            </x-th>
                            <x-th>
                                <x-input type="number" wire:model="current_correlative" />
                            </x-th>
                            <x-td class="text-center">
                                <x-button wire:click="store">
                                    <i class="fa fa-save mr-2"></i> Agregar correlativo
                                </x-button>
                            </x-td>
                        </x-tr>

                    </x-slot>
                    
                </x-table>
            @endif
        </x-spacing>
    </x-card>

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
