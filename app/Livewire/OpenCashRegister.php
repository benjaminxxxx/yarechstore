<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\CashRegister;
use App\Models\Branch;
use Session;
use Illuminate\Support\Str;

class OpenCashRegister extends Component
{
    public $initialAmount;
    public $branch;
    public $thereIsOpenCash;

    protected $rules = [
        'initialAmount' => 'required|numeric|min:0',
    ];
    public function mount()
    {
        $branchCode = Session::get('selected_branch');


        if ($branchCode) {
            $this->branch = Branch::where('code', $branchCode)->first();
        }
    }
    public function openRegister()
    {
        $this->validate();

        if ($this->branch) {

            CashRegister::where('status', 'open')->where('branch_id',$this->branch->id )->update(['status' => 'closed']);

            // Crear una nueva caja
            CashRegister::create([
                'code'=>Str::random(15),
                'initial_amount' => $this->initialAmount,
                'status' => 'open',
                'opened_at' => now(),
                'branch_id'=>$this->branch->id
            ]);

            session()->flash('message', 'La caja se ha abierto correctamente.');

            // Resetear el campo después de abrir la caja
            $this->reset('initialAmount');

        }

        // Cerrar cualquier caja abierta previamente
        
    }
    public function render()
    {
        $this->thereIsOpenCash = CashRegister::where('status', 'open')->where('branch_id',$this->branch->id )->first();
        return view('livewire.open-cash-register');
    }
}
