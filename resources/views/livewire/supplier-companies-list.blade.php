<div>
    <!--DESARROLLO SUPPLIERCOMPANY-->
    <div class="flex items-center gap-5">
        <x-h3 class="my-5">
            Compañías
        </x-h3>
        <x-button type="button" @click="$wire.dispatch('registerCompany')">
            Registrar Compañía
        </x-button>
    </div>
    <x-card>
        <x-spacing>
            <x-table>
                <x-slot name="thead">
                    <tr>
                        <x-th value="Nombre" />
                        <x-th value="RUC" />
                        <x-th value="Persona de Contacto" />
                        <x-th value="Teléfono" />
                        <x-th value="Correo" />
                        <x-th value="Dirección" />
                        <x-th value="Acciones" class="text-center" />
                    </tr>
                </x-slot>
                <x-slot name="tbody">
                    @if ($companies && $companies->count())
                        @foreach ($companies as $company)
                            <x-tr>
                                <x-td>
                                    {{ $company->name }}
                                </x-td>
                                <x-td>
                                    {{ $company->ruc }}
                                </x-td>
                                <x-td>
                                    {{ $company->contact_person }}
                                </x-td>
                                <x-td>
                                    {{ $company->phone }}
                                </x-td>
                                <x-td>
                                    {{ $company->email }}
                                </x-td>
                                <x-td>
                                    {{ $company->address }}
                                </x-td>
                                <x-td class="text-center">
                                    <div class="flex items-center justify-center gap-3">
                                        <x-button @click="$wire.dispatch('editCompany',{id:{{ $company->id }}})">
                                            <i class="fa fa-edit"></i>
                                        </x-button>
                                        <x-danger-button wire:confirm="¿Está seguro que desea eliminar esta compañía? No podrá realizar operaciones que involucren esta compañía" wire:click="delete({{ $company->id }})">
                                            <i class="fa fa-trash"></i>
                                        </x-danger-button>
                                    </div>
                                </x-td>
                            </x-tr>
                        @endforeach
                    @else
                        <tr>
                            <x-td colspan="6" class="text-center">
                                No se encontraron compañías.
                            </x-td>
                        </tr>
                    @endif
                </x-slot>
            </x-table>
        </x-spacing>
    </x-card>
</div>
