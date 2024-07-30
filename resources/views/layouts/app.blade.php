<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="v2fLMH8w3xgUEQcl63H9">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Sistema POS Ferreteria Yarech">
    <meta name="author" content="Benjamin Quispe">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="generator" content="Hugo 0.88.1">

    <title>{{ $title??'Punto de Venta' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&amp;display=swap"
        rel="stylesheet">
        <link rel="stylesheet" href="{{asset('css/custom.css')}}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{asset('image/favicon.png')}}">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="theme-color" content="#ffffff">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <script>
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia(
                '(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('v2fLMH8w3xgUEQcl63H9');
        } else {
            document.documentElement.classList.remove('v2fLMH8w3xgUEQcl63H9')
        }
    </script>
    <style>
        /* Estilos personalizados para el autocompletado */
        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus,
        input:-webkit-autofill:active {
            -webkit-box-shadow: 0 0 0 30px #f7fafc inset !important; /* Cambia el color de fondo del autocompletado */
            box-shadow: 0 0 0 30px #f7fafc inset !important;
            -webkit-text-fill-color: #2d3748 !important; /* Cambia el color del texto del autocompletado */
            font-size: 1rem !important; /* Asegúrate de que el tamaño de la fuente sea consistente */
        }
    </style>
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles
</head>

<body x-data="themeSwitcher()"  :class="{ 'dark': switchOn }">



    <nav class="fixed top-0 z-50 w-full bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700">
        <div class="px-3 py-3 lg:px-5 lg:pl-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center justify-start rtl:justify-end">
                    <button @click="isOpen = !isOpen" type="button"
                        class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600">
                        <span class="sr-only">Open sidebar</span>
                        <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path clip-rule="evenodd" fill-rule="evenodd"
                                d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z">
                            </path>
                        </svg>
                    </button>
                    <a href="https://flowbite.com" class="flex ms-2 md:me-24">
                        <img src="{{asset('image/logo.svg')}}" alt="" class="h-8">
                    </a>
                </div>
                <div class="flex items-center">
                    <div x-data="window.themeSwitcher()" x-init="switchTheme()" @keydown.window.tab="switchOn = false" class="flex items-center justify-center space-x-2">
                        <input id="thisId" type="checkbox" name="switch" class="hidden" :checked="switchOn">
                    
                        <button 
                            x-ref="switchButton"
                            type="button" 
                            @click="switchOn = ! switchOn; switchTheme()"
                            :class="switchOn ? 'bg-blue-600' : 'bg-neutral-200'" 
                            class="relative inline-flex h-6 py-0.5 ml-4 focus:outline-none rounded-full w-10">
                            <span :class="switchOn ? 'translate-x-[18px]' : 'translate-x-0.5'" class="w-5 h-5 duration-200 ease-in-out bg-white rounded-full shadow-md"></span>
                        </button>
                    
                        <label @click="$refs.switchButton.click(); $refs.switchButton.focus()" :id="$id('switch')" 
                            :class="{ 'text-blue-600': switchOn, 'text-gray-400': ! switchOn }"
                            class="text-sm select-none">
                            Dark Mode
                        </label>
                    </div>
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                <button
                                    class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                    <img class="h-8 w-8 rounded-full object-cover"
                                        src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                                </button>
                            @else
                                <span class="inline-flex rounded-md">
                                    <button type="button"
                                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus:bg-gray-50 dark:focus:bg-gray-700 active:bg-gray-50 dark:active:bg-gray-700 transition ease-in-out duration-150">
                                        {{ Auth::user()->name }}

                                        <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                        </svg>
                                    </button>
                                </span>
                            @endif
                        </x-slot>

                        <x-slot name="content">
                            <!-- Account Management -->
                            <div class="block px-4 py-2 text-xs text-gray-400">
                                {{ __('Manage Account') }}
                            </div>

                            <x-dropdown-link href="{{ route('profile.show') }}">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                                <x-dropdown-link href="{{ route('api-tokens.index') }}">
                                    {{ __('API Tokens') }}
                                </x-dropdown-link>
                            @endif

                            <div class="border-t border-gray-200 dark:border-gray-600"></div>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}" x-data>
                                @csrf

                                <x-dropdown-link href="{{ route('logout') }}" @click.prevent="$root.submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>

                </div>
            </div>
        </div>
    </nav>

    <aside
           :class="isOpen ? 'translate-x-0' : '-translate-x-full'"
           id="logo-sidebar"
           class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform bg-white border-r border-gray-200 sm:translate-x-0 dark:bg-gray-800 dark:border-gray-700"
           aria-label="Sidebar">
        <div class="h-full px-3 pb-4 overflow-y-auto bg-white dark:bg-gray-800">
            <ul>
                <x-link href="{{route('dashboard')}}" :active="request()->routeIs('dashboard')">
                    <i class="fa fa-cart-plus"></i>
                    <span class="ms-3">Vender</span>
                </x-link>
                <x-link href="{{route('products')}}" :active="request()->routeIs('products')">
                    <i class="fa fa-tools"></i>
                    <span class="ms-3">Productos</span>
                </x-link>
                <x-link href="{{route('user')}}" :active="request()->routeIs('user')">
                    <i class="fa fa-users"></i>
                    <span class="ms-3">Usuarios</span>
                </x-link>
                <x-link href="{{route('branch')}}" :active="request()->routeIs('branch')">
                    <i class="fa fa-building"></i>
                    <span class="ms-3">Sucursales</span>
                </x-link>
                <x-link href="{{route('purchases')}}" :active="request()->routeIs('purchases')">
                    <i class="fa fa-file-invoice"></i>
                    <span class="ms-3">Compras</span>
                </x-link>
                <x-link href="{{route('inventory')}}" :active="request()->routeIs('inventory')">
                    <i class="fa fa-boxes"></i>
                    <span class="ms-3">Inventario</span>
                </x-link>
            </ul>
        </div>
    </aside>

    <div class="p-4 sm:ml-64 pt-[72px] h-screen overflow-auto bg-slate-100 dark:bg-gray-700">
        {{ $slot }}
    </div>




    @stack('modals')

    @livewireScripts
</body>

</html>
