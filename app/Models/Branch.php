<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;
    protected $fillable = [
        'company_id',
        'address',
        'code',
        'name'
    ];
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }

    public function getProductsWithInventoryAttribute()
    {
        return $this->products()->with('inventories')->get()->map(function ($product) {
            $inventory = $product->inventories->where('branch_id', $this->id)->first();
            return (object) [
                'id' => $product->id,
                'name' => $product->name,
                'generic_image_url' => $product->generic_image_url,
                'unit_type' => $product->unit_type,
                'stock' => $inventory->stock ?? null,
                'minimum_stock' => $inventory->minimum_stock ?? null,
                'location' => $inventory->location ?? null,
                'expiry_date' => $inventory->expiry_date ?? null,
                'is_inventoried' => $inventory !== null
            ];
        });
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_branch')->withTimestamps();
    }
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function cart()
    {
        return $this->sales()->where('status', 'cart');
    }
    public function cashRegisterOpen()
    {
        return $this->hasOne(CashRegister::class)->where('status', 'open');
    }
    public function correlatives()
    {
        return $this->hasMany(Correlative::class);
    }
}
