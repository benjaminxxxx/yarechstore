<div>
    <x-card>
        <x-spacing>
            <div class="mb-2 md:mb-4">
                <x-button wire:click="openForm()">{{ __('Add New Unit') }}</x-button>
            </div>
            <x-table>
                <x-slot name="thead">
                    <tr>
                        <x-th value="{{ __('Name') }}" />
                        <x-th value="{{ __('Actions') }}" class="text-center" />
                    </tr>
                </x-slot>
                <x-slot name="tbody">
                    @if ($units->count())
                        @foreach ($units as $unit)
                            <x-tr>
                                <x-th value="{{ $unit->name }}" />
                                <x-td class="text-center">
                                    <div class="flex items-center justify-center">
                                        <x-button wire:click="edit({{ $unit->id }})">
                                            <i class="fa fa-pencil mr-2"></i> {{ __('Edit') }}
                                        </x-button>
                                        @if ($unit->products->count() == 0)
                                            <x-danger-button
                                                wire:confirm="{{ __('Are you sure you want to delete this unit?') }}"
                                                wire:click="delete({{ $unit->id }})" class="ml-1">
                                                <i class="fa fa-remove mr-2"></i> {{ __('Delete') }}
                                            </x-danger-button>
                                        @endif
                                    </div>
                                </x-td>
                            </x-tr>
                        @endforeach
                    @else
                        <x-tr>
                            <x-td colspan="2">{{ __('No units found.') }}</x-td>
                        </x-tr>
                    @endif
                </x-slot>
            </x-table>
        </x-spacing>
    </x-card>

    <x-dialog-modal wire:model="isFormOpen" maxWidth="lg">
        <x-slot name="title">
            {{ __('Add New Unit') }}
        </x-slot>

        <x-slot name="content">
            <form wire:submit.prevent="store">
                <div>
                    <x-label for="name">{{ __('Name') }}</x-label>
                    <x-input type="text" wire:keydown.enter="store" wire:model="name" id="name" />
                    <x-input-error for="name" />
                </div>
            </form>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button type="button" wire:click="closeForm"
                class="mr-2">{{ __('Cancel') }}</x-secondary-button>
            <x-button type="submit" wire:click="store" class="ml-3">{{ __('Save') }}</x-button>
        </x-slot>
    </x-dialog-modal>

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
