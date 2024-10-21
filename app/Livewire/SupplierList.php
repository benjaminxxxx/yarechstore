<?php

namespace App\Livewire;

use App\Models\Supplier;
use Livewire\Component;

class SupplierList extends Component
{
    public $proveedores;
    public function mount(){
        $this->proveedores = Supplier::with(['user'])->get();   
        
    }
    public function render()
    {
        return view('livewire.supplier-list');
    }
}
