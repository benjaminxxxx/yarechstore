<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Storage;

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
            // Verifica si el archivo existe en la ruta absoluta
            if (file_exists(public_path($this->generic_image_url))) {
                return asset($this->generic_image_url);
            } else {
                // Retorna un ícono predeterminado si no se encuentra el archivo
                return asset('image/tag.svg');
            }
        } else {
            // Si no hay imagen definida, retorna un avatar generado
            $avatar = "https://avatar.iran.liara.run/username?username=" . urlencode($this->name);
            return $avatar;
        }
    }

   
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_products');
    }
    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }
    public function inventory()
    {
        return $this->hasOne(Inventory::class);
    }
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_type');
    }
    public function child()
    {
        return $this->hasOne(Product::class, 'parent_id');
    }
    public function parent()
    {
        return $this->belongsTo(Product::class, 'parent_id');
    }
    public function presentations()
    {
        return $this->hasMany(Presentation::class);
    }
    public function stocks()
    {
        return $this->hasMany(ProductStock::class);
    }
}
