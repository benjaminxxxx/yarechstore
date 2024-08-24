<div>
    
    @if($document)
    <x-button-a href="{{$document}}" target="_blank">
        Imprimir Boleta
    </x-button-a>
    @else
    <x-button wire:click="createDocument">
        Boleta
    </x-button>
    @endif
</div>
