<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\SalesController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InicioController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\CorrelativeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UnitController;
use App\Http\Middleware\CheckBranches;
use App\Http\Middleware\CheckBranchSelected;

Route::middleware([
    'auth:sanctum',
    CheckBranches::class,
    CheckBranchSelected::class,
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::get('/', [InicioController::class,'index'])->name('dashboard');
    Route::get('/branch', [BranchController::class,'index'])->name('branch');
    Route::get('/select-branch', [BranchController::class, 'selectBranch'])->name('select-branch');
    Route::post('/set-branch', [BranchController::class, 'setBranch'])->name('set-branch');
    Route::get('/user', [UserController::class,'index'])->name('user');
    Route::get('/products',[ProductController::class,'index'])->name('products');
    Route::get('/purchases',[PurchaseController::class,'index'])->name('purchases');
    Route::get('/inventory',[InventoryController::class,'index'])->name('inventory');

    Route::get('/config/units',[UnitController::class,'index'])->name('config.units');
    Route::get('/config/corelatives',[CorrelativeController::class,'index'])->name('config.correlatives');
    Route::get('/company', [CompanyController::class,'index'])->name('company');
    Route::get('/sales', [SalesController::class,'index'])->name('sales');

    Route::get('/config/invoice-extra-information', [ConfigController::class,'invoiceExtraInfomracion'])->name('config.invoiceExtraInfomracion');
    Route::get('/config/site', [ConfigController::class,'site'])->name('config.site');
});
