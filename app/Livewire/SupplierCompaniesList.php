<?php

namespace App\Livewire;

use App\Models\Supplier as Company;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class SupplierCompaniesList extends Component
{
    //<!--DESARROLLO SUPPLIERCOMPANY-->
    use LivewireAlert;
    public $companies;
    protected $listeners = ['refreshList' => '$refresh'];
    public function render()
    {
        $this->companies = Company::where('user_id', Auth::id())->get();
        return view('livewire.supplier-companies-list');
    }
    public function delete($companyId){
        $company = Company::where('id',$companyId)->where('user_id',Auth::id())->first();
        if($company){
            $company->purchases()->delete();
            $company->delete();
            $this->alert('success', 'Compañía eliminada con éxito.');
        }else{
            $this->alert('error', "Usuario Inválido");
        }
    }
}
