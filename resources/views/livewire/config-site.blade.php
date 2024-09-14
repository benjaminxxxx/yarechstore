<div>
    <x-card>
        <x-spacing>
            <form wire:submit.prevent="store">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    <!-- Nombre del sitio -->
                    <div>
                        <x-label for="site_name">Nombre del sitio</x-label>
                        <x-input type="text" wire:model="site_name" id="site_name" />
                        <x-input-error for="site_name" />
                    </div>


                    <!-- Logo Principal -->
                    <div>
                        <x-label for="site_logo">Logo Principal</x-label>
                        @if ($site_logo || $site_logo_url)

                            @if ($site_logo)
                                <img src="{{ $site_logo->temporaryUrl() }}" class="max-h-16 object-cover">
                            @elseif ($site_logo_url)
                                <img src="{{ Storage::disk('public')->url($site_logo_url) }}"
                                    class="max-h-16 object-cover">
                            @endif

                            <x-danger-button type="button" class="w-auto mt-2" wire:click="deleteLogo">
                                <i class="fa fa-trash"></i>
                            </x-danger-button>
                        @else
                            <div wire:loading wire:target="site_logo">Subiendo...</div>
                            <div class="flex items-center justify-center w-full">
                                <label for="dropzone-main-logo"
                                    class="flex flex-col items-center justify-center w-full h-36 border-2 border-slate-300 border-dashed rounded-lg cursor-pointer bg-slate-100 hover:bg-slate-200">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-8 h-8 mb-4 text-gray-500" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                                        </svg>
                                        <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Subir Logo
                                                Principal</p>
                                        <p class="text-xs text-gray-500 text-center">SVG, PNG, JPG, etc.</p>
                                    </div>
                                    <input id="dropzone-main-logo" type="file" wire:model="site_logo" class="hidden"
                                        accept="image/*, .svg" />
                                </label>
                            </div>
                        @endif
                        <x-input-error for="site_logo" />
                    </div>

                    <!-- Logo Contraste -->
                    <div>
                        <x-label for="site_logo_contrast">Logo Contraste</x-label>
                        @if ($site_logo_contrast || $site_logo_contrast_url)
                            @if ($site_logo_contrast)
                                <img src="{{ $site_logo_contrast->temporaryUrl() }}" class="max-h-16 object-cover">
                            @elseif ($site_logo_contrast_url)
                                <img src="{{ Storage::disk('public')->url($site_logo_contrast_url) }}"
                                    class="max-h-16 object-cover">
                            @endif
                            <x-danger-button type="button" class="w-auto mt-2" wire:click="deleteContrastLogo">
                                <i class="fa fa-trash"></i>
                            </x-danger-button>
                        @else
                            <div wire:loading wire:target="site_logo_contrast">Subiendo...</div>
                            <div class="flex items-center justify-center w-full">
                                <label for="dropzone-contrast-logo"
                                    class="flex flex-col items-center justify-center w-full h-36 border-2 border-slate-300 border-dashed rounded-lg cursor-pointer bg-slate-100 hover:bg-slate-200">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-8 h-8 mb-4 text-gray-500" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                                        </svg>
                                        <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Subir Logo
                                                Contraste</p>
                                        <p class="text-xs text-gray-500 text-center">SVG, PNG, JPG, etc.</p>
                                    </div>
                                    <input id="dropzone-contrast-logo" type="file" wire:model="site_logo_contrast"
                                        class="hidden" accept="image/*, .svg" />
                                </label>
                            </div>
                        @endif
                        <x-input-error for="site_logo_contrast" />
                    </div>

                    <!-- Logo Horizontal -->
                    <div>
                        <x-label for="site_logo_horizontal">Logo Horizontal</x-label>
                        @if ($site_logo_horizontal || $site_logo_horizontal_url)

                            @if ($site_logo_horizontal)
                                <img src="{{ $site_logo_horizontal->temporaryUrl() }}" class="max-h-16 object-cover">
                            @elseif ($site_logo_horizontal_url)
                                <img src="{{ Storage::disk('public')->url($site_logo_horizontal_url) }}"
                                    class="max-h-16 object-cover">
                            @endif

                            <x-danger-button type="button" class="w-auto mt-2" wire:click="deleteHorizontalLogo">
                                <i class="fa fa-trash"></i>
                            </x-danger-button>
                        @else
                            <div wire:loading wire:target="site_logo_horizontal">Subiendo...</div>
                            <div class="flex items-center justify-center w-full">
                                <label for="dropzone-horizontal-logo"
                                    class="flex flex-col items-center justify-center w-full h-36 border-2 border-slate-300 border-dashed rounded-lg cursor-pointer bg-slate-100 hover:bg-slate-200">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-8 h-8 mb-4 text-gray-500" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                                        </svg>
                                        <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Subir Logo
                                                Horizontal</p>
                                        <p class="text-xs text-gray-500 text-center">SVG, PNG, JPG, etc.</p>
                                    </div>
                                    <input id="dropzone-horizontal-logo" type="file"
                                        wire:model="site_logo_horizontal" class="hidden" accept="image/*, .svg" />
                                </label>
                            </div>
                        @endif
                        <x-input-error for="site_logo_horizontal" />
                    </div>

                    <!-- site_favicon -->
                    <div>
                        <x-label for="site_favicon">Favicon</x-label>
                        @if ($site_favicon || $site_favicon_url)

                            @if ($site_favicon)
                                <img src="{{ $site_favicon->temporaryUrl() }}" class="max-h-16 object-cover">
                            @elseif ($site_favicon_url)
                                <img src="{{ Storage::disk('public')->url($site_favicon_url) }}"
                                    class="max-h-16 object-cover">
                            @endif

                            <x-danger-button type="button" class="w-auto mt-2" wire:click="deleteFavicon">
                                <i class="fa fa-trash"></i>
                            </x-danger-button>
                        @else
                            <div wire:loading wire:target="site_favicon">Subiendo...</div>
                            <div class="flex items-center justify-center w-full">
                                <label for="dropzone-site_favicon"
                                    class="flex flex-col items-center justify-center w-full h-36 border-2 border-slate-300 border-dashed rounded-lg cursor-pointer bg-slate-100 hover:bg-slate-200">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-8 h-8 mb-4 text-gray-500" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                            <path stroke="currentColor" stroke-linecap="round"
                                                stroke-linejoin="round" stroke-width="2"
                                                d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                                        </svg>
                                        <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Subir
                                                site_favicon</p>
                                        <p class="text-xs text-gray-500 text-center">PNG, JPG, etc.</p>
                                    </div>
                                    <input id="dropzone-site_favicon" type="file" wire:model="site_favicon"
                                        class="hidden" accept="image/*" />
                                </label>
                            </div>
                        @endif
                        <x-input-error for="site_favicon" />
                    </div>
                </div>

                <div class="mt-5">
                    <x-button type="submit">Guardar cambios</x-button>
                </div>
            </form>
        </x-spacing>
    </x-card>
</div>
