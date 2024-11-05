<div>
    <div class="bg-slate-100 flex items-center justify-center h-screen ">
        <div
            class="w-full max-w-4xl m-auto shadow-lg rounded-lg overflow-hidden md:flex items-center h-screen lg:max-h-[36rem]">
            <!-- Left Panel -->
            <div class="w-full md:w-1/2 bg-white p-8 relative h-full">
                @php
                    // Obtener el logo horizontal desde SiteConfig
                    $logoHorizontalUrl =
                        $siteConfig && $siteConfig->site_logo_horizontal
                            ? Storage::disk('public')->url($siteConfig->site_logo_horizontal)
                            : asset('image/logo.svg');
                @endphp

                <div class="absolute top-0 left-0 p-8">
                    <img src="{{ $logoHorizontalUrl }}" alt="Logo" class="h-16">
                </div>
                <div class="flex items-center justify-center h-full">
                    <div class="max-w-80 w-full">
                        <x-validation-errors class="mb-4" />

                        @session('status')
                            <div class="mb-4 font-medium text-sm text-secondaryText">
                                {{ $value }}
                            </div>
                        @endsession
                        <div class="md:hidden mb-10">
                            <img src="{{ asset('image/logo.svg') }}" alt="Logo" class="h-7">
                        </div>
                        <h1 class="hidden md:block mb-10 font-bold text-2xl text-secondaryText">Vamos a iniciar!</h1>
                        <form wire:submit="iniciarSesion">
                            <div>
                                <x-input type="email" wire:model="email" placeholder="Email" required autofocus />
                            </div>

                            <div class="mt-4">
                                <x-input type="password" wire:model="password" placeholder="Contraseña" required />
                            </div>

                            <div class="block my-5">
                                <label for="remember_me" class="flex items-center">
                                    <x-checkbox id="remember_me" wire:model="remember" />
                                    <span class="ms-2 text-sm">Recordar sesión</span>
                                </label>
                            </div>

                            @if (session()->has('error'))
                                <div class="text-sm my-3 text-red-600">
                                    {{ session('error') }}
                                </div>
                            @endif

                            <x-button type="submit" class="w-full justify-center">
                                Ingresar
                            </x-button>
                        </form>

                        <div>
                            @if (Route::has('password.request'))
                                <a class="underline text-sm rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-secondary dark:focus:ring-offset-gray-800"
                                    href="{{ route('password.request') }}">
                                    ¿Olvidó su contraseña?
                                </a>
                            @endif
                        </div>

                        <div class="inline-flex items-center justify-center w-full">
                            <hr class="w-64 h-px my-8 bg-gray-200 border-0 dark:bg-gray-700">
                            <span
                                class="absolute px-3 font-medium text-gray-900 -translate-x-1/2 bg-white left-1/2 dark:text-white dark:bg-gray-900">ó</span>
                        </div>

                        <div>
                            <button type="button"
                                class="w-full flex items-center justify-center gap-3 border border-1 border-gray-400 hover:bg-gray-50 focus:ring-4 focus:outline-none focus:ring-[#4285F4]/50 font-medium rounded-lg text-sm px-5 py-2.5">
                                <img src="{{ asset('image/google.png') }}" width="20px" alt="Sign with Google" />
                                Continue with Google
                            </button>
                        </div>



                        <div class="mt-4">
                            <a href="{{ route('login') }}"
                                class="w-full justify-center underline text-bindigo-600 text-center block">
                                Volver
                            </a>
                        </div>

                    </div>
                </div>
            </div>
            <!-- Right Panel -->
            <div class="hidden md:block w-full md:w-1/2 flex items-center justify-center h-full">
                <img src="{{ asset('image/banner_supplier.webp') }}" class="h-full w-full object-cover"
                    alt="Yarech Supplier Logo">
            </div>
        </div>
    </div>
</div>
