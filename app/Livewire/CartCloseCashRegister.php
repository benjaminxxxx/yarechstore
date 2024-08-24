<?php

namespace App\Livewire;

use App\Models\Branch;
use Livewire\Component;
use Auth;
use Session;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class CartCloseCashRegister extends Component
{
    use LivewireAlert;
    public $isFormOpen = false;
    public $branch;
    public $sales;
    public $cashRegisterOpen;
    public $totalSales;
    public $initial_and_current_amount;
    public $changes;
    public $salesAmount;
    public $difference;
    public function mount(){
        $branchCode = Session::get('selected_branch');
        if ($branchCode) {
            $this->branch = Branch::where('code', $branchCode)->first();
        }

        if($this->branch){
            $this->cashRegisterOpen = $this->branch->cashRegisterOpen;
        }
    }
    public function render()
    {
        return view('livewire.cart-close-cash-register');
    }
    public function openModalCashRegister(){

        if(!$this->branch){
            $this->alert('error', 'La sesión ha terminado');
            return;
        }

        if(!$this->cashRegisterOpen){
            $this->alert('error', 'No hay Caja Abierta Disponible');
            return;
        }

        $this->sales = $this->cashRegisterOpen->sales;

        $this->changes = (float)$this->cashRegisterOpen->cashTransactions()->where('type','expense')->sum('amount');
        $this->salesAmount = (float)$this->cashRegisterOpen->cashTransactions()->where('type','sale')->sum('amount');

        $this->initial_and_current_amount = $this->cashRegisterOpen->initial_amount + $this->cashRegisterOpen->current_amount;
        
        $this->difference = $this->initial_and_current_amount - $this->cashRegisterOpen->initial_amount - $this->salesAmount;
        $this->isFormOpen = true;
    }
    public function closeModalCashRegister()
    {
        $this->isFormOpen = false;
    }
    public function confirmCloseCashRegister(){
        if(!$this->cashRegisterOpen){
            $this->alert('error', 'No hay Caja Abierta Disponible');
            return;
        }
        $this->cashRegisterOpen->status='close';
        $this->cashRegisterOpen->save();
        return redirect()->route('dashboard');
    }
}
