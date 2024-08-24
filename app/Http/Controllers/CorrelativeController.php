<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CorrelativeController extends Controller
{
    public function index(){
        return view('config.correlative');
    }
}
