<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'stock',
        'location'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
