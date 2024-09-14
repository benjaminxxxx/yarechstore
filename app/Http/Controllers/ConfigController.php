<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ConfigController extends Controller
{
    public function invoiceExtraInfomracion(){
        return view('config.invoice-extra-information');
    }
    public function site(){
        return view('config.site');
    }
}
