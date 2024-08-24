<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'cash_register_id',
        'type',
        'amount',
        'description',
        'sale_id'
    ];

    public function cashRegister()
    {
        return $this->belongsTo(CashRegister::class);
    }
}
