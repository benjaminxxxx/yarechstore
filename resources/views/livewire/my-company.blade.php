<div>

    <x-card>
        <x-spacing>
            <form wire:submit.prevent="store" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                <div class="mt-3">
                    <x-label for="name">Nombre</x-label>
                    <x-input type="text" wire:model="name" id="name" />
                    <x-input-error for="name" />
                </div>
                <div class="mt-3">
                    <x-label for="ruc">RUC</x-label>
                    <x-input type="text" wire:model="ruc" id="ruc" />
                    <x-input-error for="ruc" />
                </div>
                <div class="mt-3">
                    <x-label for="address">Dirección</x-label>
                    <x-input type="text" wire:model="address" id="address" />
                    <x-input-error for="address" />
                </div>
                <div class="mt-3">
                    <x-label for="sol_user">Usuario SOL</x-label>
                    <x-input type="text" wire:model="sol_user" id="sol_user" />
                    <x-input-error for="sol_user" />
                </div>
                <div class="mt-3">
                    <x-label for="sol_pass">Clave SOL</x-label>
                    <x-input type="password" wire:model="sol_pass" id="sol_pass" />
                    <x-input-error for="sol_pass" />
                </div>
                
                <div class="mt-3">
                    <x-label for="client_secret">Client Secret</x-label>
                    <x-input type="password" wire:model="client_secret" id="client_secret" autocomplete="off" />
                    <x-input-error for="client_secret" />
                </div>
                <div class="mt-3">
                    <x-label for="production">Modo</x-label>
                    <x-select wire:model="production" id="production">
                        <option value="0">En desarrollo</option>
                        <option value="1">En Producción</option>
                    </x-select>
                    <x-input-error for="production" />
                </div>
                <div class="mt-3">
                    <x-label for="sert_path">Certificado</x-label>
                    @if ($cert_path || $cert_path_url)

                        <img src="{{ asset('image/pemfile.png') }}" class="w-auto max-h-16  object-cover">
                        @if ($cert_path_url)
                            <a href="{{ Storage::disk('public')->url($cert_path_url) }}"
                                class="text-indigo-600 underline font-bold my-3" target="_blank">[Descargar Certificado]</a>
                        @endif
                        <x-danger-button type="button" class="w-auto mt-2" wire:click="deleteCert">
                            <i class="fa fa-trash"></i>
                        </x-danger-button>
                    @else
                        <div wire:loading wire:target="sert_path">Subiendo...</div>
                        <div class="flex items-center justify-center w-full">
                            <label for="dropzone-file"
                                class="flex flex-col items-center justify-center w-full h-36 border-2 border-slate-300 border-dashed rounded-lg cursor-pointer bg-slate-100 dark:hover:bg-gray-800 dark:bg-gray-700 hover:bg-slate-200 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                                    </svg>
                                    <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span
                                            class="font-semibold">Subir Certificado</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 text-center">Pem, Txt</p>
                                </div>
                                <input id="dropzone-file" type="file" wire:model="cert_path" class="hidden"
                                    accept=".pem" />
                            </label>
                        </div>
                    @endif
                    <x-input-error for="sert_path" />
                </div>
                <div class="mt-3 text-right col-span-1 md:cols-span-2 lg:col-span-3">
                    <x-button type="submit">Actualizar información</x-button>
                </div>
            </form>
        </x-spacing>
    </x-card>
</div>
