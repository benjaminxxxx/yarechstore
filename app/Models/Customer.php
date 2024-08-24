<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $fillable = [
        'fullname',
        'document_type_id', // Cambiado para permitir letras y números
        'document_number',
        'phone',
        'address',
        'department',
        'province',
        'email',
        'district',
        'customer_type_id',
        'commercial_name',
        'billing_ruc',
        'billing_address',
        'points',
        'notes',
    ];

    public function customerType()
    {
        return $this->belongsTo(CustomerType::class);
    }
    public function documentType()
    {
        return $this->belongsTo(DocumentSunatType::class, 'document_type_id', 'code');
    }
}
