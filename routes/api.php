<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\InvoiceController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('invoices/send', [InvoiceController::class, 'send']);
Route::post('invoices/xml', [InvoiceController::class, 'xml'])->middleware('auth:api');
Route::post('invoices/pdf', [InvoiceController::class, 'pdf']);