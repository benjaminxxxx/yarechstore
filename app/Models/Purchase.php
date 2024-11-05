<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'purchase_date',
        'invoice_code',
        'receipt_code',
        'operation_number',
        'total_amount',
        'status',
        'branch_id',
        'xml_file',
        'tax_amount',
        'sub_total'
    ];

    public function details()
    {
        return $this->hasMany(PurchaseDetail::class);
    }

    public function defectiveProducts()
    {
        return $this->hasMany(DefectiveProduct::class);
    }
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
