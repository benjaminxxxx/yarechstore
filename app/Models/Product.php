<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'code',
        'name',
        'barcode',
        'sunatcode',
        'description',
        'generic_image_url',
        'unit_type',
        'units_per_package',
        'igv',
        'base_price',
        'purchase_price',
        'final_price',
        'is_active',
        'weight',
        'dimensions',
        'brand_id',
        'supplier_id',
        'branch_id',
        'parent_id',
        'created_by',
        'updated_by',
    ];
    public function getPhotoUrlAttribute()
    {
        if ($this->generic_image_url) {
            // Assuming $this->generic_image_url contains a path relative to 'public/uploads/'
            return asset($this->generic_image_url);
        } else {
            $avatar = "https://avatar.iran.liara.run/username?username=" . $this->name;
            return $avatar;
        }
    }

   
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_products');
    }

}
