<?php

namespace App\Livewire;

use App\Models\InvoiceExtraInformation;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class ConfigInvoiceExtraInformation extends Component
{
    use LivewireAlert;
    public $types;
    public $name = [];
    public $value = [];
    public $toDelete;
    protected $listeners = ['confirmed' => 'delete'];
    public function mount()
    {
        $this->types = [
            'header' => 'Cabecera',
            'extra' => 'Información Adicional',
            'footer' => 'Pie de Página',
        ];
    }
    public function render()
    {

        $informations = InvoiceExtraInformation::get()->groupBy('type');
        
        return view('livewire.config-invoice-extra-information', [
            'InvoiceExtraInformations' => $informations,
        ]);
    }
    public function store($type)
    {

        try {
            InvoiceExtraInformation::create([
                'type' => $type,
                'name' => $this->name[$type],
                'value' => $this->value[$type],
            ]);

            $this->alert('success', 'Parámetros Agregados');
        } catch (\Throwable $th) {
            $this->alert('error', 'Ocurrió un error: ' . $th->getMessage());
        }
    }
    public function askDelete($id)
    {
        $this->toDelete = $id;

        $this->alert('question', '¿Seguro que desea eliminar este parámetro?', [
            'showConfirmButton' => true,
            'showCancelButton' => true,
            'confirmButtonText' => 'Si',
            'cancelButtonText' => 'No, Cancelar',
            'onConfirmed' => 'confirmed',
            'onDismissed' => 'cancelled',
            'position' => 'center',
            'toast' => false,
            'timer' => null,
            'confirmButtonColor' => '#F5922A',
            'cancelButtonColor' => '#2C2C2C',
        ]);
    }
    public function delete()
    {
        InvoiceExtraInformation::find($this->toDelete)->delete();
    }
}
