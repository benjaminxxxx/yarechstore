<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
    public function login(){

        if (Auth::check()) {
            // Si ya está autenticado, redirige al dashboard
            return redirect()->route('dashboard');
        }


        return view('auth.supplier');
    }
    public function index(){
        return view('supplier.index');
    }
    public function dashboard(){
        return view('supplier.index');
    }
    public function companies(){
        return view('supplier.companies');
    }
    public function xml(){
        return view('supplier.xml');
    }
}
