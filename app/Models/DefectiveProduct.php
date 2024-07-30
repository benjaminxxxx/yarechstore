<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DefectiveProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'purchase_id',
        'defect_date',
        'defect_reason',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
}
