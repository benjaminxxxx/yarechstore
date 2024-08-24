<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Correlative extends Model
{
    use HasFactory;
    protected $fillable = [
        'series', 
        'branch_id', 
        'invoice_type_id', 
        'current_correlative'
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function invoiceType()
    {
        return $this->belongsTo(InvoicesType::class, 'invoice_type_id');
    }

    public function incrementCorrelative()
    {
        $this->increment('current_correlative');
    }
}
