<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

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
        'cash_register_id'
    ];
    
    public function invoiceType()
    {
        return $this->belongsTo(InvoicesType::class,'invoice_type_id');
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
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    protected function totalAmountFormat(): Attribute
    {
        return Attribute::get(fn() => 'S/. ' . number_format($this->total_amount, 2, '.', ','));
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
