<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Storage;

class Sale extends Model
{
    use HasFactory;
    protected $fillable = [
        'code',
        'customer_id',
        'customer_name',
        'customer_document',
        'status',
        'subtotal',
        'total_amount',
        'igv',
        'payment_method',
        'document_status',
        'document_type_id',
        'document_code',
        'branch_id',
        'invoice_type_id',
        'document_correlative',
        'xml_path',
        'signed_xml_path',
        'cdr_path',
        'document_path',
        'cash',
        'cash_register_id',
        'emision_date',
        'pay_date'
    ];

    public function invoiceType()
    {
        return $this->belongsTo(InvoicesType::class, 'invoice_type_id');
    }
    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function documentType()
    {
        return $this->belongsTo(DocumentType::class);
    }
    public function paymentMethods()
    {
        return $this->hasMany(PaymentMethod::class);
    }
    public function prepayments()
    {
        return $this->hasMany(Prepayment::class);
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    protected function totalAmountFormat(): Attribute
    {
        return Attribute::get(fn() => 'S/. ' . number_format($this->total_amount, 2, '.', ','));
    }
    public function getClientAttribute()
    {
        return $this->customer ? $this->customer->fullname : 'Cliente Varios';
    }
    public function getDocumentPathOficialAttribute()
    {
        if ($this->document_path) {
            // Usar pathinfo para dividir la ruta y manipularla
            $pathInfo = pathinfo($this->document_path);

            // Construir la nueva ruta añadiendo '_oficial' antes de la extensión
            $documentPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '_oficial.' . $pathInfo['extension'];

            // Verificar si el archivo existe en el disco 'public'
            if (Storage::disk('public')->exists($documentPath)) {
                // Retornar la URL completa si el archivo existe
                return $documentPath;
            }
        }


        // Retornar null si el archivo no existe
        return null;
    }
    public function getTotalItemsAttribute()
    {
        return $this->items ? $this->items->count() : 0;
    }
    protected function statusFormat(): Attribute
    {
        return Attribute::get(fn() => match ($this->status) {
            'cart' => 'En Carrito',
            'paid' => 'Pagado',
            'canceled' => 'Cancelado',
            'debt' => 'Deuda',
            default => ucfirst($this->status),
        });
    }
    protected function saleName(): Attribute
    {
        return Attribute::get(function () {
            // Si invoice_type_id es null, generar un nombre de voucher
            if (is_null($this->invoice_type_id)) {
                return 'Voucher V' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
            }

            // Obtener el tipo de documento (Factura o Boleta)
            $invoiceType = $this->invoiceType;
            $documentPrefix = match ($invoiceType?->code) {
                '01' => 'Factura ',
                '03' => 'Boleta ',
                default => 'Voucher ',
            };

            // Generar el número de documento
            $documentNumber = $this->document_code ? $this->document_code . '-' : 'V';
            $documentCorrelative = str_pad($this->document_correlative ?? $this->id, 5, '0', STR_PAD_LEFT);

            // Combinar todo en un solo nombre
            return $documentPrefix . $documentNumber . $documentCorrelative;
        });
    }
}
