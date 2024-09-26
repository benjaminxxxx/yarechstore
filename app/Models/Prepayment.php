<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prepayment extends Model
{
    use HasFactory;
    protected $table = 'prepayments';

    // Campos permitidos para inserción masiva
    protected $fillable = [
        'sale_id',
        'amount',
        'related_doc_type',
        'related_doc_number',
        'total',
        'xml_file',
        'signed_xml_file',
        'cdr_file',
        'document_path',
        'file_path'
    ];

    // Relación con la tabla de ventas
    public function sale()
    {
        return $this->belongsTo(Sale::class, 'sale_id');
    }
    public function getHasDocumentAttribute()
    {
        // Verifica si el archivo existe en el sistema de almacenamiento público
        if ($this->file_path && \Storage::disk('public')->exists($this->file_path)) {
            // Retorna la URL completa al archivo
            return \Storage::disk('public')->url($this->file_path);
        }

        // Retorna null si el archivo no existe
        return null;
    }
    public function getHasDocumentVoucherAttribute()
    {
        // Verifica si el archivo existe en el sistema de almacenamiento público
        if ($this->document_path && \Storage::disk('public')->exists($this->document_path)) {
            // Retorna la URL completa al archivo
            return \Storage::disk('public')->url($this->document_path);
        }

        // Retorna null si el archivo no existe
        return null;
    }
}
