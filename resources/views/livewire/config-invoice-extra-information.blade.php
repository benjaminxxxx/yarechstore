<div>
    @foreach ($types as $idType => $type)
        <x-card>
            <x-spacing>
                <x-h2>
                    {{ $type }}
                </x-h2>
                <x-table>
                    <x-slot name="thead">
                        <tr>
                            <x-th value="name" />
                            <x-th value="value" />
                            <x-th value="" class="text-center" />
                        </tr>
                    </x-slot>
                    <x-slot name="tbody">
                       
                        @if (isset($InvoiceExtraInformations[$idType]) && $InvoiceExtraInformations[$idType]->count() > 0)
                            @foreach ($InvoiceExtraInformations[$idType] as $InvoiceExtraInformation)
                                <x-tr>

                                    <x-th value="{{ $InvoiceExtraInformation->name }}" />
                                    <x-th value="{{ $InvoiceExtraInformation->value }}" />
                                    <x-td class="text-center">
                                        <x-danger-button wire:click="askDelete('{{ $InvoiceExtraInformation->id }}')"
                                            class="ml-1">
                                            <i class="fa fa-trash"></i>
                                        </x-danger-button>
                                    </x-td>

                                </x-tr>
                            @endforeach
                        @else
                            <x-tr>
                                <x-td colspan="4">No se encontraron Parámetros.</x-td>
                            </x-tr>
                        @endif
                        <x-tr>
                            <x-th>
                                <x-input type="text" wire:model="name.{{ $idType }}" />
                            </x-th>
                            <x-th>
                                <x-input type="text" wire:model="value.{{ $idType }}" />
                            </x-th>
                            <x-td class="text-center">
                                <x-button wire:click="store('{{ $idType }}')">
                                    Guardar
                                </x-button>
                            </x-td>
                        </x-tr>
                    </x-slot>
                </x-table>
            </x-spacing>
        </x-card>
    @endforeach

</div>
