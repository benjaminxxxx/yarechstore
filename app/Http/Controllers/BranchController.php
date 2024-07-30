<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Branch;

class BranchController extends Controller
{
    public function index(){
        return view('branch');
    }
    public function selectBranch()
    {
        $branches = Auth::user()->branches;

        return view('select-branch', compact('branches'));
    }
    public function setBranch(Request $request)
    {
        
        $request->validate([
            'branch_code' => 'required|exists:branches,code',
        ]);
       
        Session::put('selected_branch', $request->branch_code);
        return redirect()->route('dashboard');
    }
}
