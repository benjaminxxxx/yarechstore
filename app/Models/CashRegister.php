<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashRegister extends Model
{
    use HasFactory;
    protected $fillable = [
        'code',
        'user_id',
        'initial_amount',
        'current_amount',
        'opened_at',
        'closed_at',
        'status',
        'branch_id'
    ];
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cashTransactions()
    {
        return $this->hasMany(CashTransaction::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }
    protected function initialAmountFormat(): Attribute
    {
        return Attribute::get(fn() => 'S/. ' . number_format($this->initial_amount, 2, '.', ','));
    }
    protected function currentAmountFormat(): Attribute
    {
        return Attribute::get(fn() => 'S/. ' . number_format($this->current_amount, 2, '.', ','));
    }
}
