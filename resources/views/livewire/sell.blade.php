<div>
    <div class="2xl:flex h-full p-3 bg-gray-50">
        <!-- Main Content -->
        <div class="flex-1 p-4 mb-5">
            <div class="relative">
                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none text-indigo-700">
                    <i class="fa fa-search"></i>
                </div>
                <input type="search" id="default-search"
                    class="block w-full p-3 bg-white ps-10 text-sm text-gray-800 border-0 rounded-2xl focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-indigo-500 dark:focus:border-indigo-500"
                    placeholder="Busca tus productos aquí" required />

            </div>

            <div
                class="text-sm font-medium text-center text-gray-800 border-b border-gray-200 dark:text-gray-400 dark:border-gray-700">
                <ul class="flex flex-wrap -mb-px">
                    <li class="me-2">
                        <a href="#"
                            class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300">Profile</a>
                    </li>
                    <li class="me-2">
                        <a href="#"
                            class="inline-block p-4 font-bold border-b-2 border-amber-500 rounded-t-lg active dark:text-yellow-500 dark:border-yellow-500"
                            aria-current="page">Dashboard</a>
                    </li>
                    <li class="me-2">
                        <a href="#"
                            class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300">Settings</a>
                    </li>
                    <li class="me-2">
                        <a href="#"
                            class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300">Contacts</a>
                    </li>
                    <li>
                        <a
                            class="inline-block p-4 text-gray-400 rounded-t-lg cursor-not-allowed dark:text-gray-500">Disabled</a>
                    </li>
                </ul>
            </div>
            <div class="max-h-[45rem] overflow-y-auto py-5 pr-3 pretty-scroll">
                <div class="grid grid-cols-12 gap-10">
                    @for ($y = 1; $y < 30; $y++)
                        <x-card class="bg-white col-span-6 md:col-span-4 lg:col-span-3">
                            <x-spacing class="text-center">
                                <img src="{{asset('image/burger.png')}}" class="w-auto" alt=""/>
                                <p class="py-2"><b>Amburguesa</b></p>
                                <p><b class="text-indigo-600">$750</b></p>
                            </x-spacing>
                        </x-card>
                    @endfor
                </div>
            </div>
        </div>

        <!-- Cart Sidebar -->
        <x-card class="w-full xl:w-80 2xl:w-[30rem] h-full bg-white flex flex-col justify-between">
            <x-spacing>
                <div class="pedidos">
                    <div class="bg-indigo-600 text-white py-3 px-4 mb-3 shadow-lg rounded-lg font-bold">
                        Compra N°1
                    </div>
                </div>

                <div class="relative overflow-auto">
                    <table class="w-full text-md text-left">
                        <thead>
                            <tr>
                                <th scope="col">
                                    <x-h5>Items</x-h5>
                                </th>
                                <th scope="col" class="text-center">
                                    <x-h5>Cnt</x-h5>
                                </th>
                                <th scope="col" class="text-right">
                                    <x-h5>Precio</x-h5>
                                </th>
                                <th scope="col">
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @for ($x = 1; $x < 7; $x++)
                                <tr>
                                    <th scope="row"
                                        class="py-2 font-normal text-gray-900 whitespace-nowrap dark:text-white">
                                        Apple MacBook Pro 17"
                                    </th>
                                    <td class="px-1 py-4 text-center">
                                        <div class="relative flex items-center justify-center">
                                            <x-counter-button class="rounded-s text-sm">
                                                <i class="fa fa-minus"></i>
                                            </x-counter-button>
                                            <x-counter-input type="text" autocomplete="off" />
                                            <x-counter-button class="rounded-e text-sm">
                                                <i class="fa fa-plus"></i>
                                            </x-counter-button>
                                        </div>
                                    </td>
                                    <td class="px-1 py-4 text-right">
                                        <b>$2999</b>
                                    </td>
                                    <td class="px-1 py-4 text-right">
                                        <a href="#"
                                            class="font-medium text-red-600 dark:text-blue-500 hover:underline">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </x-spacing>

            <x-spacing class="bg-slate-200 dark:bg-slate-800 mt-auto">
                <table class="w-full">
                    <tfoot>
                        <tr>
                            <th>
                                <x-label class="text-left">
                                    Sub total
                                </x-label>
                            </th>
                            <th>
                                <x-label class="text-right">
                                    $1200
                                </x-label>
                            </th>
                        </tr>
                        <tr>
                            <th>
                                <x-label class="text-left">
                                    IGV
                                </x-label>
                            </th>
                            <th>
                                <x-label class="text-right">
                                    $45
                                </x-label>
                            </th>
                        </tr>
                        <tr>
                            <th>
                                <x-h5 class="text-left">
                                    Total
                                </x-h5>
                            </th>
                            <th>
                                <x-h5 class="text-right">
                                    $1500
                                </x-h5>
                            </th>
                        </tr>
                    </tfoot>
                </table>

                <div class="md:flex justify-between mt-5">
                    <x-secondary-button class="dark:bg-slate-900">
                        Guardar
                    </x-secondary-button>
                    <x-secondary-button class="dark:bg-slate-900">
                        Pagar
                    </x-secondary-button>
                    <x-secondary-button class="dark:bg-slate-900">
                        Pagar después
                    </x-secondary-button>
                </div>
            </x-spacing>
        </x-card>

    </div>
</div>
