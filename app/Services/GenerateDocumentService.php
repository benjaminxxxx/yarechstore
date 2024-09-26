<?php

namespace App\Services;

use App\Models\InvoicesType;
use App\Models\Prepayment;
use App\Models\Sale;
use App\Models\Company as ModelsCompany;
use App\Models\PaymentMethod;
use App\Models\DigitalTransaction;
use App\Models\Correlative;
use App\Services\SunatService;
use DateTime;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Storage;
use Luecano\NumeroALetras\NumeroALetras;
use Greenter\Report\XmlUtils;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class GenerateDocumentService
{
    public $sale;
    protected $company;
    protected $typeDocument;
    protected $invoiceType;

    public function __construct()
    {
        $this->company = ModelsCompany::firstOrFail();
    }
    public function generateDocumentTitle()
    {
        // Definir un título por defecto
        $text_document = 'RECIBO DE VENTA';

        // Obtener el tipo de documento de la venta
        $this->invoiceType = InvoicesType::find($this->sale->invoice_type_id);

        if ($this->invoiceType) {
            $this->typeDocument = $this->invoiceType->code;
            // Definir el prefijo según el código del tipo de documento
            switch ($this->typeDocument) {
                case '01':
                    $text_document = "FACTURA DE VENTA ELECTRONICA";
                    break;
                case '03':
                    $text_document = "BOLETA DE VENTA ELECTRONICA";
                    break;
            }

            // Si se encuentra un prefijo válido, añadir la numeración
            if ($text_document !== 'RECIBO DE VENTA') {
                $numerocorrelativo = $this->sale->document_correlative;
                $text_document .= " {$this->sale->document_code} - {$numerocorrelativo}";
            }
        }

        return $text_document;
    }

    public function createSimpleVoucher($saleId,$type,$options = [])
    {
        $this->sale = Sale::find($saleId);

        if(!$this->sale){
            return;
        }

        $itemsFromSale = $this->sale->items()->get();
        $filename = null;
        $invoiceName = isset($options['invoice_name'])?$options['invoice_name']:null;
        $anticipo = isset($options['anticipo'])?$options['anticipo']:null;
        $anticipo_id = isset($options['anticipo_id'])?$options['anticipo_id']:null;

        if($invoiceName){
            $filename = Carbon::now()->format('Y/m') . '/' . $invoiceName . '.pdf';
        }else{
            $filename = $this->sale->document_path; //2024/08/Boleta_B001_0000002_voucher.pdf || 2024/08/V00002_voucher.pdf
        }

        $items = [];

        foreach ($itemsFromSale as $item) {
            // Agregar el ítem al array de items con el formato deseado
            $items[] = [
                'code' => sprintf('P%03d', $item->product_id), // Generar código basado en el ID del producto
                'description' => $item->product_name, // Descripción del producto
                'quantity' => $item->quantity, // Cantidad del producto
                'unit_price' => (float) $item->product_price, // Precio unitario del producto
                'discount' => 0.00, // No hay descuento según los datos proporcionados
                'subtotal' => (float) $item->subtotal, // Subtotal del producto
                'igv' => (float) $item->igv, // IGV del producto
                'total' => (float) $item->total_price, // Total del producto
            ];
        }
        
        $title = "";
        
        if($type=='NORMAL'){
            $title = $this->generateDocumentTitle();
        }
        if($type=='PORPAGAR'){
            $title = "VOUCHER DE VENTA POR PAGAR";
        }

        $primerPago = 0;
        $porpagar = 0;
        $totalPagado = $this->getTotalPagado($this->sale->id);
        $vuelto = $this->sale->cash;

        if($type=='PARCIALBOLETA'){
            $title = "VOUCHER DE BOLETA CON PAGO PARCIAL";
            


            $methodsAddedObject = PaymentMethod::where('sale_id', $this->sale->id)->get();

            if ($methodsAddedObject->count() > 0) {
                $methodsAdded = $methodsAddedObject->keyBy('method')->toArray();
                
                $paymentMethods = collect($methodsAdded)->filter(function ($method, $key) {
                    return $method['amount'] > 0 && $key !== 'client';
                });
                $primerPago = $paymentMethods->sum('amount');
                $porpagar = $totalPagado-$vuelto-$primerPago;
                
            }
        }
        if($type=='PARCIALFACTURA'){
            $title = "VOUCHER DE FACTURA CON PAGO PARCIAL";
            


            $methodsAddedObject = PaymentMethod::where('sale_id', $this->sale->id)->get();

            if ($methodsAddedObject->count() > 0) {
                $methodsAdded = $methodsAddedObject->keyBy('method')->toArray();
                
                $paymentMethods = collect($methodsAdded)->filter(function ($method, $key) {
                    return $method['amount'] > 0 && $key !== 'client';
                });
                $primerPago = $paymentMethods->sum('amount');
                $porpagar = $totalPagado-$vuelto-$primerPago;
                
            }
        }
        

        $data = [
            'text_document' => $title,
            'date' => Date('d/m/Y'),
            'company_name' => $this->company->name,
            'company_ruc' => $this->company->ruc,
            'company_address' => $this->company->address,
            'items' => $items,
            'op_gravada' => $this->sale->subtotal,
            'igv' => $this->sale->igv,
            'total_amount' => $this->sale->total_amount,
            'total_pagado' => $totalPagado,
            'vuelto' => $vuelto,
            'type'=>$type,
            'pagado_parcial'=>$primerPago,
            'por_pagar'=>$porpagar
        ];
        

        if ($this->sale->customer_id != null) {
            $data['client'] = [
                "tipoDoc" => (string) $this->sale->customer_document_type,
                "numDoc" => $this->sale->customer_document,
                "rznSocial" => $this->sale->customer_name
            ];
        }else{
            $data['client'] = [
                "tipoDoc" => "1",
                "numDoc" => "00000000",
                "rznSocial" => "VARIOS"
            ];
        }

        // Renderizar la vista y generar el PDF
        $pdf = Pdf::loadView('documents.boleta', $data);

        $width = 80 / 25.4 * 72; // Convertir 80 mm a puntos
        $height = 200 / 25.4 * 72; // Longitud de 300 mm convertida a puntos (ajústala según la necesidad)
        $pdf->setPaper([0, 0, $width, $height], 'portrait');


        if (!$filename) {
            $filename = $this->getFileName();
        }

        Storage::disk('public')->put($filename, $pdf->output());
        if($type=='PARCIALFACTURA' || $type=='PARCIALBOLETA'){
            if($anticipo && $anticipo_id){
                $prepayment = Prepayment::find($anticipo_id);
                if($prepayment){
                    $prepayment->document_path = $filename;
                    $prepayment->save();
                }
            }
        }else{
            $this->sale->document_path = $filename;
            $this->sale->save();
        }
        
    }
    public function getFileName()
    {
        $idFormatted = 'V' . str_pad($this->sale->id, 8, '0', STR_PAD_LEFT);

        // Formato de fecha: YYYY/MM
        $filename = Carbon::now()->format('Y/m') . '/';

        // Obtener el tipo de documento (factura o boleta)
        $this->invoiceType = InvoicesType::find($this->sale->invoice_type_id);

        if ($this->invoiceType) {
            $idFormatted = '';

            // Definir el prefijo del nombre del archivo según el tipo de documento
            if ($this->invoiceType->code == '01') {
                $filename .= 'Factura_';
            } elseif ($this->invoiceType->code == '03') {
                $filename .= 'Boleta_';
            }

            // Agregar el código del documento (por ejemplo, F001-)
            if ($this->sale->document_code) {
                $filename .= $this->sale->document_code . '_';
            }

            // Agregar el correlativo del documento, formateado a 7 dígitos (por ejemplo, 0000001)
            if ($this->sale->document_correlative) {
                $filename .= str_pad($this->sale->document_correlative, 7, '0', STR_PAD_LEFT);
            }
        }

        // Añadir el sufijo de voucher y la extensión PDF
        $filename .= $idFormatted . '_voucher.pdf';

        return $filename;
    }

    public function getTotalPagado($saleId)
    {
        // Obtener los métodos de pago asociados a la venta
        $paymentMethods = PaymentMethod::where('sale_id', $saleId)->get();

        // Sumar los montos de todos los métodos de pago
        $totalPagado = $paymentMethods->sum('amount');

        return $totalPagado;
    }
    
    
    public function setLegends(&$data)
    {
        $formatter = new NumeroALetras();

        $data['legends'] = [
            [
                'code' => '1000',
                'value' => $formatter->toInvoice($data['mtoImpVenta'], 2, 'SOLES')
            ]
        ];
    }
    
}
