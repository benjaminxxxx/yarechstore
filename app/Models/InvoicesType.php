<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoicesType extends Model
{
    use HasFactory;
    protected $table = "invoices_type";
    
    protected $fillable = [
        'name',
        'code',
        'description',
    ];

    /**
     * Get the invoices that belong to this type.
     */
    public function invoices()
    {
        return $this->hasMany(Correlative::class, 'invoice_type_id');
    }
}
