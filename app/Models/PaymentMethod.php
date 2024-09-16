<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;
    protected $fillable = [
        'sale_id',
        'method',
        'amount'
    ];
    public function getMethodNameAttribute()
    {
        // Diccionario de equivalentes en español
        $methods = [
            'cash' => 'Efectivo',
            'card' => 'Tarjeta',
            'yape' => 'Yape',
            'plin' => 'Plin',
            'client' => 'Crédito Cliente',
            'bank_transfer' => 'Transferencia Bancaria',
            'deposit' => 'Depósito',
            'check' => 'Cheque',
            'bim' => 'BIM',
        ];

        // Retornar el nombre equivalente en español y formato camelCase
        $method = $this->attributes['method'];
        return isset($methods[$method]) ? $methods[$method] : ucfirst($method);
    }
}
