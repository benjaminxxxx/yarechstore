<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ConfigController extends Controller
{
    public function InvoiceExtraInfomracion(){
        return view('config.invoice-extra-information');
    }
}
