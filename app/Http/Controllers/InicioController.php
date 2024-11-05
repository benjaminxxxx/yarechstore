<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use App\Models\CashRegister;
use App\Models\Branch;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;

class InicioController extends Controller
{

    public function index()
    {

        $branchCode = Session::get('selected_branch');

        if ($branchCode) {

            $branch = Branch::where('code',$branchCode)->first();

            if($branch){
                if($this->isCashRegisterEnabled()){

                
                    $cashRegister = CashRegister::where('status', 'open')->where('branch_id',$branch->id)->first();
        
                    if (!$cashRegister) {
                        return view("cash_register.open");
                    }
                }
        
                return view("sell");
            }
            
        }

    }
    public function isCashRegisterEnabled()
    {
        return env('USE_CASH_REGISTER', false);
    }
}
