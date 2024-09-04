<x-guest-layout>
    <div class="bg-slate-100 flex items-center justify-center h-screen ">
        <div class="w-full max-w-6xl m-auto shadow-lg rounded-lg overflow-hidden md:flex items-center h-screen lg:max-h-[36rem]">
            <!-- Left Panel -->
            <div class="hidden md:block w-full md:w-3/5 bg-white p-8 relative h-full">
                <div class="absolute top-0 left-0 p-8">
                    <img src="{{ asset('image/logo.svg') }}" alt="Logo" class="h-7">
                </div>
                <div class="flex items-center justify-center h-full">
                    <img src="{{ asset('image/login-main.svg') }}" alt="Large Logo" style="width:60%">
                </div>
            </div>
            <!-- Right Panel -->
            <div class="w-full md:w-2/5 p-8 flex items-center justify-center h-full bg-primary">
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
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div>
                            <x-input id="email" class="block mt-1 w-full" type="email" name="email"
                                :value="old('email')" placeholder="Email" required autofocus />
                        </div>

                        <div class="mt-4">
                            <x-input id="password"  placeholder="Contraseña" class="block mt-1 w-full" type="password" name="password" required
                                 />
                        </div>

                        <div class="block my-5">
                            <label for="remember_me" class="flex items-center">
                                <x-checkbox id="remember_me" name="remember" />
                                <span
                                    class="ms-2 text-sm text-secondaryText">Recordar sesión</span>
                            </label>
                        </div>

                        <div class="">


                            <x-secondary-button type="submit" class="w-full justify-center">
                                INGRESAR
                            </x-secondary-button>
                        </div>

                        <div>
                            @if (Route::has('password.request'))
                                <a class="underline text-sm text-secondaryText hover:text-secondaryText rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-secondary dark:focus:ring-offset-gray-800"
                                    href="{{ route('password.request') }}">
                                    ¿Olvidó su contraseña?
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</x-guest-layout>
