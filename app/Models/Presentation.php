<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presentation extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'barcode',
        'unit',
        'description',
        'factor',
        'price',
    ];

    /**
     * Relación con el modelo Product.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function units()
    {
        return $this->belongsTo(Unit::class,'unit');
    }
}
