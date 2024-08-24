<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentSunatType extends Model
{
    use HasFactory;
    protected $primaryKey = 'code';
    public $incrementing = false; // Indica que no es autoincremental
    protected $keyType = 'string'; // Indica que es de tipo string

    // Los campos que se pueden asignar masivamente
    protected $fillable = ['code', 'name', 'short_name'];

    // Relación con el modelo Customer
    public function customers()
    {
        return $this->hasMany(Customer::class, 'document_type_id', 'code');
    }
}
