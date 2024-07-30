<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;
    protected $table = "inventory";
    protected $fillable = [
        'product_id',
        'stock',
        'minimum_stock',
        'location',
        'expiry_date',
        'branch_id'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
