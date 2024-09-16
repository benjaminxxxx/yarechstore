<div>
    <x-card>
        <x-spacing>
            <div class="mb-2 md:mb-4 flex items-center">
                <x-button wire:click="openForm()">Agregar Nuevo Producto</x-button>
                <form class="shadow-lg ml-3">
                    <label for="default-search"
                        class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>
                    <div class="relative">
                        <div
                            class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none text-primary">
                            <i class="fa fa-search"></i>
                        </div>
                        <x-input type="search" wire:model.live="search" id="default-search"
                            class="pl-10" autocomplete="off" placeholder="Buscar" required />
                    </div>
                </form>
            </div>
        </x-spacing>
    </x-card>
    <x-card class="mt-5">
        <x-spacing>
            <div class="grid grid-cols-12 gap-4">
                @if ($products && $products->count() > 0)
                    @foreach ($products as $product)
                        <div
                            class="relative group bg-white col-span-12 md:col-span-4 lg:col-span-3 2xl:col-span-2 hover-show rounded-lg overflow-hidden shadow-md">
                            <!-- Tarjeta del producto -->
                            <x-card class="relative h-full">
                                <x-spacing class="text-center">
                                    <img src="{{ $product->photo_url }}" class="w-auto m-auto" alt="" />
                                    <p class="py-2"><b>{{ $product->name }}</b></p>
                                    <p><b class="text-secondary">S/ {{ $product->final_price }}</b></p>
                                    <div class="flex lg:hidden items-center justify-center gap-5 mt-3">
                                        <x-button wire:click="see({{ $product->id }})">
                                            <i class="fa fa-eye mr-2"></i>
                                            Ver
                                        </x-button>
                                        <x-button wire:click="duplicate({{ $product->id }})">
                                            <i class="fa fa-copy mr-2"></i>
                                            Duplicar
                                        </x-button>
                                    </div>
                                </x-spacing>
                            </x-card>

                            <!-- Contenedor de los botones, que aparece en hover -->
                            <div
                                class="lg:absolute lg:inset-0 items-center justify-center hidden lg:flex gap-2 lg:bg-opacity-20 lg:bg-black p-4 hover-options">
                                <x-option-button wire:click="see('{{ $product->code }}')">
                                    <i class="fa fa-eye mr-2"></i>
                                    Ver
                                </x-option-button>
                                <x-option-button wire:click="duplicate({{ $product->id }})">
                                    <i class="fa fa-copy mr-2"></i>
                                    Duplicar
                                </x-option-button>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </x-spacing>
    </x-card>
    
    <livewire:view-product/>

    <!-- Modal -->
    
   
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
