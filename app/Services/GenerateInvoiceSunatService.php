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
use Illuminate\Support\Facades\Log;

class GenerateInvoiceSunatService
{
    protected $sunatService;
    protected $sale;
    protected $company;
    protected $typeDocument;
    protected $invoiceType;

    public function __construct()
    {
        $this->sunatService = new SunatService();
        $this->company = ModelsCompany::firstOrFail();
    }

    public function generateNextCorrelative()
    {
        if ($this->sale->document_code != null)
            return;

        // Buscar el correlativo asociado al tipo de documento y sucursal
        $correlative = Correlative::where('invoice_type_id', $this->sale->invoice_type_id)
            ->where('branch_id', $this->sale->branch_id)
            ->first();

        if ($correlative) {

            $nextCorrelative = $correlative->current_correlative + 1;
            // Actualizar serie y correlativo en la venta
            $this->sale->document_code = $correlative->series;
            $this->sale->document_correlative = $nextCorrelative;

            // Guardar los cambios en la base de datos
            $correlative->current_correlative = $nextCorrelative;
            $correlative->save();
        }
    }
    /*
    public function process(Sale $sale)
    {

        $response = [];
        $this->sale = $sale;
        $this->invoiceType = InvoicesType::find($this->sale->invoice_type_id);

        $this->generateNextCorrelative();

        $date = new DateTime('now', new \DateTimeZone('America/Lima'));
        $fechaEmision = $date->format('Y-m-d\TH:i:sP');


        $details = [];
        $itemsFromSale = $this->sale->items;

        foreach ($itemsFromSale as $item) {
            // Calcular los valores necesarios
            $mtoValorUnitario = (float) $item['unit_value'];
            $mtoBaseIgv = (float) $item['subtotal'];
            $porcentajeIgv = (float) $item['percent_igv'];
            $igv = (float) $item['igv'];
            $total_taxes = (float) $item['total_taxes']; // IGV calculado previamente
            $mtoValorVenta = (float) $mtoBaseIgv; // Generalmente igual al subtotal
            $mtoPrecioUnitario = (float) $item['product_price']; // Precio unitario con IGV

            // Añadir el detalle del ítem al array de detalles
            $details[] = [
                "tipAfeIgv" => 10, // Tipo de afectación del IGV (10 = Gravado - Operación Onerosa)
                "codProducto" => sprintf('P%03d', $item->product_id), // Código del producto
                "unidad" => "NIU", // Unidad de medida (ejemplo: "NIU" = Unidad)
                "descripcion" => $item['product_name'], // Descripción del producto
                "cantidad" => $item['quantity'], // Cantidad de productos
                "mtoValorUnitario" => $mtoValorUnitario, // Monto valor unitario sin IGV
                "mtoValorVenta" => $mtoValorVenta, // Monto de la venta sin IGV
                "mtoBaseIgv" => $mtoBaseIgv, // Base imponible para el IGV
                "porcentajeIgv" => $porcentajeIgv, // Porcentaje de IGV aplicado
                "igv" => $igv, // Monto del IGV
                "totalImpuestos" => $total_taxes, // Total de impuestos (en este caso, solo IGV)
                "mtoPrecioUnitario" => $mtoPrecioUnitario // Monto del precio unitario con IGV
            ];
        }

        if ($this->invoiceType) {
          
            $this->typeDocument = $this->invoiceType->code;

            $data = [
                "ublVersion" => "2.1",
                "tipoDoc" => $this->typeDocument,
                "tipoOperacion" => "0101",
                "serie" => $this->sale->document_code,
                "correlativo" => $this->sale->document_correlative,
                "fechaEmision" => $fechaEmision,
                "formaPago" => [
                    "moneda" => "PEN",
                    "tipo" => "Contado"
                ],
                "tipoMoneda" => "PEN",

                "company" => [
                    "ruc" => 20611263300,
                    "razonSocial" => "INVERSIONES YARECH S.R.L.",
                    "nombreComercial" => "-",
                    "address" => [
                        "ubigueo" => "040307",
                        "departamento" => "AREQUIPA",
                        "provincia" => "CARAVELI",
                        "distrito" => "CHALA",
                        "urbanizacion" => "-",
                        "direccion" => "AV. LAS FLORES MZA. 17 LOTE. 4 A.H.  FLORES",
                        "codLocal" => "0000"
                    ]
                ],

                "client" => [
                    "tipoDoc" => '1',
                    "numDoc" => '00000000',
                    "rznSocial" => 'VARIOS'
                ],

                "mtoOperGravadas" => (float) $this->sale->subtotal,
                "mtoIGV" => (float) $this->sale->igv,
                "totalImpuestos" => (float) $this->sale->igv,
                "valorVenta" => (float) $this->sale->subtotal,
                "subTotal" => (float) $this->sale->total_amount,
                "mtoImpVenta" => (float) $this->sale->total_amount,
                "details" => $details,
                "legends" => [
                    [
                        "code" => "1000",
                        "value" => ""
                    ]
                ]
            ];
            if ($this->sale->customer_id != null) {
                $data['client'] = [
                    "tipoDoc" => (string) $this->sale->customer_document_type,
                    "numDoc" => $this->sale->customer_document,
                    "rznSocial" => $this->sale->customer_name
                ];
            }

           
            $this->setTotales($data);
            $this->setLegends($data);

            $sunat = new SunatService;
            $see = $sunat->getSee($this->company);

            $invoice = $sunat->getInvoice($data);

            if(!$this->sale->cdr_path){
                $result = $see->send($invoice);

                $response['xml'] = $see->getFactory()->getLastXml();
    
                $response['hash'] = (new XmlUtils())->getHashSign($response['xml']);
    
                $response['sunatResponse'] = $sunat->sunatResponse($result);

                if (isset($response['xml'])) {

                    $signed_xml_filename = Carbon::now()->format('Y/m') . '/' . $invoice->getName() . '.xml';
    
                    Storage::disk('public')->put($signed_xml_filename, $response['xml']);
                    $this->sale->signed_xml_path = $signed_xml_filename;
                    $this->sale->save();
                }
    
                if (isset($response['sunatResponse']['cdrZip'])) {
    
                    $filename = Carbon::now()->format('Y/m') . '/' . $invoice->getName() . '.zip';
    
                    Storage::disk('public')->put($filename, base64_decode($response['sunatResponse']['cdrZip']));
                    $this->sale->cdr_path = $filename;
                    $this->sale->save();
                }
    
                if ($response['sunatResponse']['success'] == true) {
                    $this->sale->emision_date = Carbon::now()->format('Y/m/d');
                    if($this->sale->status=='paid'){
                        $this->sale->pay_date  = Carbon::now()->format('Y/m/d');
                    }
                    $this->sale->save();
                    
                    $this->createSimpleVoucher($invoice->getName());
                }else{
                    $this->createSimpleVoucher();
                }
            }else{
                $this->createSimpleVoucher();
            }

            $htmlInvoice = $sunat->getHtmlReport($invoice);

            $this->htmlToPdf($htmlInvoice,$invoice->getName());

            //$documentCorrelative = str_pad($this->sale->document_correlative, 6, '0', STR_PAD_LEFT);

            
        } else {
           
            $this->createSimpleVoucher();
        }


        return $response;
    }*/
    public function process(Sale $sale, $options = [])
    {
        $response = [];
        $this->sale = $sale;
        $this->invoiceType = InvoicesType::find($this->sale->invoice_type_id);

        $emitFactura = isset($options['emitFactura']) ? $options['emitFactura'] : true;
        $regenerate = isset($options['regenerate']) ? $options['regenerate'] : false;
        $emitRecibo = isset($options['emitRecibo']) ? $options['emitRecibo'] : true;
        $emitFacturaAnticipo = isset($options['emitFacturaAnticipo']) ? $options['emitFacturaAnticipo'] : false;

        
        try {
            

            if($regenerate){
                $options['fechaEmision'] = $this->sale->emision_date;
            }
      
            if (!isset($options['fechaEmision'])) {
                throw new \Exception("Debe Ingresar una Fecha de Emisión Válida");
            }
            
            
            $fechaEmision = $options['fechaEmision'];

            

            if ($this->sale->status == 'paid') {

                

                $optionsVoucher = [];

                if ($emitFactura || $regenerate) {

                    $options = [
                        'sale_id' => $this->sale->id,
                        'fecha' => $fechaEmision
                    ];
                
                    $responseInvoice = $this->createInvoice($options);

                    if (!$responseInvoice['status']) {
                        throw new \Exception($responseInvoice['message']);
                    }

                    $optionsVoucher['invoice_name'] = isset($responseInvoice['invoice_name'])?$responseInvoice['invoice_name']:null;
                    $optionsVoucher['anticipo'] = isset($responseInvoice['anticipo'])?$responseInvoice['anticipo']:false;
                    $optionsVoucher['anticipo_id'] = isset($responseInvoice['anticipo_id'])?$responseInvoice['anticipo_id']:null;
                }

                if ($emitRecibo || $regenerate) {
                 
                    $this->createSimpleVoucher("NORMAL",$optionsVoucher);
                }
                if(!$regenerate){
                    $this->sale->pay_date  = Carbon::now()->format('Y/m/d');
                }
                
                $this->sale->save();

                $response['status'] = true;

            } elseif ($this->sale->status == 'debt') {
                $response['status'] = true;
                $methodsAddedObject = PaymentMethod::where('sale_id', $sale->id)->get();

                if ($methodsAddedObject->count() > 0) {
                    $methodsAdded = $methodsAddedObject->keyBy('method')->toArray();

                    $paymentMethods = collect($methodsAdded)->filter(function ($method, $key) {
                        return $method['amount'] > 0 && $key !== 'client';
                    });

                    // Revisar si hay método cliente con monto mayor a cero
                    $hasClient =
                        array_key_exists('client', $methodsAdded) &&
                        $methodsAdded['client']['amount'] > 0;
                    $hasOtherThanClient = $paymentMethods->count() > 0;

                    $anticipo_amount = $paymentMethods->sum('amount');

                    if ($hasClient) {
                        if (!$hasOtherThanClient) {
                            $optionsVoucher = [];
                            if ($emitFactura) {
                                $options = [
                                    'sale_id' => $this->sale->id,
                                    'fecha' => $fechaEmision
                                ];

                                $responseInvoice = $this->createInvoice($options);

                                if (!$responseInvoice['status']) {
                                    throw new \Exception($responseInvoice['message']);
                                }

                                $optionsVoucher['invoice_name'] = isset($responseInvoice['invoice_name'])?$responseInvoice['invoice_name']:null;
                                $optionsVoucher['anticipo'] = isset($responseInvoice['anticipo'])?$responseInvoice['anticipo']:false;
                                $optionsVoucher['anticipo_id'] = isset($responseInvoice['anticipo_id'])?$responseInvoice['anticipo_id']:null;

                            }else{
                                $prepayment = Prepayment::create([
                                    'sale_id' => $this->sale->id,
                                    'amount' => $anticipo_amount,
                                    'related_doc_type' => null,
                                    'related_doc_number' => null,
                                    'total' => $this->sale->total_amount,
                                    'signed_xml_file' => null,
                                    'cdr_file' => null,
                                ]);
                                if($prepayment){
                                    $optionsVoucher['anticipo'] = true;
                                    $optionsVoucher['anticipo_id'] = $prepayment->id;
                                }
                            }
                            if ($emitRecibo) {

                                $optionsVoucher['title'] = "RECIBO DE VENTA POR PAGAR";
                                $this->createSimpleVoucher("PORPAGAR", $optionsVoucher);
                            }
                        } else {
                            //Factura como acnticipo
                            //voucher mas
                            
                            $this->typeDocument = $this->invoiceType->code;
                            $optionsVoucher = [];

                            if($emitFacturaAnticipo){
                                $options = [
                                    'sale_id' => $this->sale->id,
                                    'listItems' => false,
                                    'description' => 'PRIMER PAGO DE VENTA',
                                    'anticipo' => true,
                                    'anticipo_amount' => $anticipo_amount,
                                    'fecha' => $fechaEmision

                                ];
                                $responseInvoice = $this->createInvoice($options);
                                if (!$responseInvoice['status']) {
                                    throw new \Exception($responseInvoice['message']);
                                }
                                $optionsVoucher['invoice_name'] = isset($responseInvoice['invoice_name'])?$responseInvoice['invoice_name']:null;
                                $optionsVoucher['anticipo'] = isset($responseInvoice['anticipo'])?$responseInvoice['anticipo']:false;
                                $optionsVoucher['anticipo_id'] = isset($responseInvoice['anticipo_id'])?$responseInvoice['anticipo_id']:null;
                                
                            }else{
                                $prepayment = Prepayment::create([
                                    'sale_id' => $this->sale->id,
                                    'amount' => $anticipo_amount,
                                    'related_doc_type' => null,
                                    'related_doc_number' => null,
                                    'total' => $this->sale->total_amount,
                                    'signed_xml_file' => null,
                                    'cdr_file' => null,
                                ]);
                                if($prepayment){
                                    $optionsVoucher['anticipo'] = true;
                                    $optionsVoucher['anticipo_id'] = $prepayment->id;
                                }
                            }

                            if ($this->typeDocument == '01') {
                                
                                if ($emitRecibo) {
                                    $this->createSimpleVoucher("PARCIALFACTURA",$optionsVoucher);
                                }
                            }
                            if ($this->typeDocument == '03') {
                               
                                if ($emitRecibo) {
                                    $this->createSimpleVoucher("PARCIALBOLETA",$optionsVoucher);
                                }
                            }

                        }
                    }
                }

            }

        } catch (\Throwable $th) {
            $response = [
                'message' => $th->getMessage(),
                'status' => false,
            ];
        }

        return $response;
    }
    public function createSimpleVoucher($type = "NORMAL", $options)
    {
        $generateDocumentService = new GenerateDocumentService();
        $generateDocumentService->createSimpleVoucher($this->sale->id, $type, $options);
    }
    public function createInvoice($options)
    {

        $response = [];
        $correlative = null;

        try {

            if (!isset($options['sale_id'])) {
                throw new \Exception("El id de la Venta es Obligatorio");
            }
            if (!isset($options['fecha'])) {
                throw new \Exception("El atributo Fecha es Obligatorio");
            }

            $sale = Sale::find($options['sale_id']);
            $serie = '';
            $nextCorrelative = '';
            $listItems = isset($options['listItems']) ? $options['listItems'] : true;

            $anticipo = isset($options['anticipo']) ? $options['anticipo'] : false;
            $fecha = $options['fecha'];
            $anticipo_amount = 0;

            if ($anticipo) {
                if (!isset($options['anticipo_amount'])) {
                    throw new \Exception("Se necesita saber el monto del anticipo.");
                } else {
                    $anticipo_amount = $options['anticipo_amount'];
                }
                if (!isset($options['description'])) {
                    throw new \Exception("Se necesita saber la descripción del anticipo.");
                }
            }

            if (!$sale) {
                throw new \Exception("La Venta No Existe");
            }

            $invoiceType = InvoicesType::find($sale->invoice_type_id);

            if (!$invoiceType) {
                throw new \Exception("No Existe el Registro Tipo de Documento");
            }

            if ($sale->document_code != null) {
                $serie = $sale->document_code;
                $nextCorrelative = $sale->document_correlative;
            } else {
                $correlative = Correlative::where('invoice_type_id', $sale->invoice_type_id)->where('branch_id', $sale->branch_id)->first();
                if (!$correlative) {
                    throw new \Exception("No hay Correlativo Registrado para la operación");
                }

                $serie = $correlative->series;
                $nextCorrelative = $correlative->current_correlative + 1;


            }

            $date = new DateTime($fecha, new \DateTimeZone('America/Lima'));
            $date->setTime(date('H'), date('i'), date('s'));
            $fechaEmision = $date->format('Y-m-d\TH:i:sP');
            $fechaEmisionGuardado = $date->format('Y-m-d');


            $details = [];


            if ($listItems) {
                $itemsFromSale = $this->sale->items;

                foreach ($itemsFromSale as $item) {
                    // Calcular los valores necesarios
                    $mtoValorUnitario = (float) $item['unit_value'];
                    $mtoBaseIgv = (float) $item['subtotal'];
                    $porcentajeIgv = (float) $item['percent_igv'];
                    $igv = (float) $item['igv'];
                    $total_taxes = (float) $item['total_taxes']; // IGV calculado previamente
                    $mtoValorVenta = (float) $mtoBaseIgv; // Generalmente igual al subtotal
                    $mtoPrecioUnitario = (float) $item['product_price']; // Precio unitario con IGV

                    // Añadir el detalle del ítem al array de detalles
                    $details[] = [
                        "tipAfeIgv" => 10, // Tipo de afectación del IGV (10 = Gravado - Operación Onerosa)
                        "codProducto" => sprintf('P%03d', $item->product_id), // Código del producto
                        "unidad" => "NIU", // Unidad de medida (ejemplo: "NIU" = Unidad)
                        "descripcion" => $item['product_name'], // Descripción del producto
                        "cantidad" => $item['quantity'], // Cantidad de productos
                        "mtoValorUnitario" => $mtoValorUnitario, // Monto valor unitario sin IGV
                        "mtoValorVenta" => $mtoValorVenta, // Monto de la venta sin IGV
                        "mtoBaseIgv" => $mtoBaseIgv, // Base imponible para el IGV
                        "porcentajeIgv" => $porcentajeIgv, // Porcentaje de IGV aplicado
                        "igv" => $igv, // Monto del IGV
                        "totalImpuestos" => $total_taxes, // Total de impuestos (en este caso, solo IGV)
                        "mtoPrecioUnitario" => $mtoPrecioUnitario // Monto del precio unitario con IGV
                    ];
                }
            } else {
                if ($anticipo) {
                    // Detalle para un anticipo
                    $anticipoAmount = (float) $options['anticipo_amount']; // Monto del anticipo con IGV
                    $anticipoDescription = $options['description'] ?? 'Adelanto'; // Descripción del anticipo

                    // Calcular el monto sin IGV (subtotal) dividiendo entre 1.18
                    $mtoBaseIgv = $anticipoAmount / 1.18;
                    $igv = $anticipoAmount - $mtoBaseIgv; // IGV es la diferencia entre el total y el subtotal

                    // El detalle del anticipo en el comprobante
                    $details[] = [
                        "tipAfeIgv" => 10, // Tipo de afectación del IGV (10 = Gravado - Operación Onerosa)
                        "codProducto" => "ANTICIPO", // Código del producto o anticipo
                        "unidad" => "NIU", // Unidad de medida
                        "descripcion" => $anticipoDescription, // Descripción del anticipo
                        "cantidad" => 1, // El anticipo es único, por lo que la cantidad es 1
                        "mtoValorUnitario" => $mtoBaseIgv, // El valor del anticipo sin IGV
                        "mtoValorVenta" => $mtoBaseIgv, // El monto del adelanto sin IGV
                        "mtoBaseIgv" => $mtoBaseIgv, // Base imponible para el IGV
                        "porcentajeIgv" => 18, // El porcentaje de IGV (18%)
                        "igv" => $igv, // Monto del IGV (18% del subtotal)
                        "totalImpuestos" => $igv, // Total de impuestos (solo IGV)
                        "mtoPrecioUnitario" => $anticipoAmount // Monto con IGV incluido (monto total)
                    ];


                }
            }


            $typeDocument = $invoiceType->code;

            $data = [
                "ublVersion" => "2.1",
                "tipoDoc" => $typeDocument,
                "tipoOperacion" => $anticipo ? "0101" : "0101", //por el momento no se puede utilizar 0104 como indica la regulacion de la sunat
                "serie" => $serie,
                "correlativo" => $nextCorrelative,
                "fechaEmision" => $fechaEmision,
                "formaPago" => [
                    "moneda" => "PEN",
                    "tipo" => "Contado"
                ],
                "tipoMoneda" => "PEN",

                "company" => [
                    "ruc" => 20611263300,
                    "razonSocial" => "INVERSIONES YARECH S.R.L.",
                    "nombreComercial" => "-",
                    "address" => [
                        "ubigueo" => "040307",
                        "departamento" => "AREQUIPA",
                        "provincia" => "CARAVELI",
                        "distrito" => "CHALA",
                        "urbanizacion" => "-",
                        "direccion" => "AV. LAS FLORES MZA. 17 LOTE. 4 A.H.  FLORES",
                        "codLocal" => "0000"
                    ]
                ],

                "client" => [
                    "tipoDoc" => '1',
                    "numDoc" => '00000000',
                    "rznSocial" => 'VARIOS'
                ],

                "mtoOperGravadas" => (float) $sale->subtotal,
                "mtoIGV" => (float) $sale->igv,
                "totalImpuestos" => (float) $sale->igv,
                "valorVenta" => (float) $sale->subtotal,
                "subTotal" => (float) $sale->total_amount,
                "mtoImpVenta" => (float) $sale->total_amount,
                "details" => $details,
                "legends" => [
                    [
                        "code" => "1000",
                        "value" => ""
                    ]
                ]
            ];


            if ($this->sale->customer_id != null) {
                $data['client'] = [
                    "tipoDoc" => (string) $sale->customer_document_type,
                    "numDoc" => $sale->customer_document,
                    "rznSocial" => $sale->customer_name
                ];
            }

            if ($anticipo) {
                $data['operGravadas'] = $mtoBaseIgv;
                $data['igvAmount'] = $igv;
                $data['totalImpuestos'] = $igv;
                $data['valorVenta'] = $mtoBaseIgv;
                $data['subTotal'] = $anticipoAmount;
                $data['mtoImpVenta'] = $anticipoAmount;
            }
            //dd($data);

            $this->setTotales($data);
            $this->setLegends($data);

            $sunat = new SunatService;
            $see = $sunat->getSee($this->company);

            $invoice = $sunat->getInvoice($data);

            $signed_xml_filename = null;
            $cdr_path = null;

            if (!$sale->cdr_path) {
                $result = $see->send($invoice);

                $response['xml'] = $see->getFactory()->getLastXml();

                $response['hash'] = (new XmlUtils())->getHashSign($response['xml']);

                $response['sunatResponse'] = $sunat->sunatResponse($result);

                if (isset($response['xml'])) {

                    $signed_xml_filename = Carbon::now()->format('Y/m') . '/' . $invoice->getName() . '.xml';

                    Storage::disk('public')->put($signed_xml_filename, $response['xml']);
                }

                if (isset($response['sunatResponse']['cdrZip'])) {

                    $cdr_path = Carbon::now()->format('Y/m') . '/' . $invoice->getName() . '.zip';
                    Storage::disk('public')->put($cdr_path, base64_decode($response['sunatResponse']['cdrZip']));
                }

                if ($response['sunatResponse']['success'] == true) {

                    if ($anticipo) {
                        //Registrar en anticipos
                        $prepayment = Prepayment::create([
                            'sale_id' => $sale->id,
                            'amount' => $anticipo_amount,
                            'related_doc_type' => $serie,
                            'related_doc_number' => $nextCorrelative,
                            'total' => $sale->total_amount,
                            'signed_xml_file' => $signed_xml_filename,
                            'cdr_file' => $cdr_path,
                        ]);

                        $response['anticipo'] = true;
                        $response['anticipo_id'] = $prepayment->id;

                        $htmlInvoice = $sunat->getHtmlReport($invoice);
                        $this->htmlToPdf($htmlInvoice, $invoice->getName(),$prepayment->id);

                    } else {
                        $sale->signed_xml_path = $signed_xml_filename;
                        $sale->cdr_path = $cdr_path;
                        $sale->document_code = $serie;
                        $sale->document_correlative = $nextCorrelative;

                        $response['anticipo'] = false;

                        $htmlInvoice = $sunat->getHtmlReport($invoice);
                        $this->htmlToPdf($htmlInvoice, $invoice->getName());

                    }
                    $sale->emision_date = $fechaEmisionGuardado;
                    $sale->save();

                    if ($correlative) {
                        $correlative->current_correlative = $nextCorrelative;
                        $correlative->save();
                    }

                } else {
                    throw new \Exception($response['sunatResponse']['error']['code'] . ' - ' . $response['sunatResponse']['error']['message']);

                }
            }

            $response['invoice_name'] = $invoice->getName();
            $response['status'] = true;

        } catch (\Throwable $th) {

            Log::error('Error en el proceso:', [
                'message' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine()
            ]);

            $response = [
                'message' => $th->getMessage(),
                'status' => false,
            ];
        }

        return $response;

    }
    public function htmlToPdf($html, $invoiceName,$inPrepayment = null)
    {

        // Configura Dompdf
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);

        // Configura el tamaño de papel y la orientación
        $dompdf->setPaper('A4', 'portrait');

        // Renderiza el PDF
        $dompdf->render();

        // Obtén el contenido del PDF
        $output = $dompdf->output();

        // Genera la ruta para almacenar el archivo
        $filePath = date('Y/m') . '/' . $invoiceName . '_oficial.pdf';

        // Guarda el archivo en el disco público
        Storage::disk('public')->put($filePath, $output);

        if($inPrepayment!=null){
            $prepayment = Prepayment::find($inPrepayment);
            if($prepayment){
                $prepayment->file_path = $filePath;
                $prepayment->save();
            }
        }else{
            $this->sale->xml_path = $filePath;
            $this->sale->save();
        }

        // Retorna la URL del archivo guardado
        return Storage::disk('public')->url($filePath);
    }
    public function setTotales(&$data)
    {
        $details = collect($data['details']);

        $data['mtoOperGravadas'] = $details->where('tipAfeIgv', 10)->sum('mtoValorVenta');
        $data['mtoOperExoneradas'] = $details->where('tipAfeIgv', 20)->sum('mtoValorVenta');
        $data['mtoOperInafectas'] = $details->where('tipAfeIgv', 30)->sum('mtoValorVenta');
        $data['mtoOperExportacion'] = $details->where('tipAfeIgv', 40)->sum('mtoValorVenta');
        $data['mtoOperGratuitas'] = $details->whereNotIn('tipAfeIgv', [10, 20, 30, 40])->sum('mtoValorVenta');

        $data['mtoIGV'] = $details->whereIn('tipAfeIgv', [10, 20, 30, 40])->sum('igv');

        $data['mtoIGVGratuitas'] = $details->whereNotIn('tipAfeIgv', [10, 20, 30, 40])->sum('igv');
        $data['icbper'] = $details->sum('icbper');
        $data['totalImpuestos'] = $data['mtoIGV'] + $data['icbper'];

        $data['valorVenta'] = $details->whereIn('tipAfeIgv', [10, 20, 30, 40])->sum('mtoValorVenta');
        $data['subTotal'] = $data['valorVenta'] + $data['totalImpuestos'];

        $data['mtoImpVenta'] = floor($data['subTotal'] * 10) / 10;

        $data['redondeo'] = $data['mtoImpVenta'] - $data['subTotal'];
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
    //Catálogo No. 51: Código de tipo de operación
    /*
    0101 Venta interna                      Factura, Boletas
    0102 Venta Interna – Anticipos          Factura, Boletas
    0103 Venta interna - Itinerante         Factura, Boletas
    0110 Venta Interna - Sustenta Traslado de Mercadería - Remitente Factura, Boletas
    0111 Venta Interna - Sustenta Traslado de Mercadería - Transportista Factura, Boletas
    0112 Venta Interna - Sustenta Gastos Deducibles Persona Natural Factura
    0120 Venta Interna - Sujeta al IVAP Factura, Boletas
    0121 Venta Interna - Sujeta al FISE Todos
    0122 Venta Interna - Sujeta a otros impuestos Todos
    0130 Venta Interna - Realizadas al Estado Factura, Boletas
    0200 Exportación de Bienes Factura, Boletas
    0201 Exportación de Servicios – Prestación servicios realizados Factura, Boletas
    12
    íntegramente en el país
    0202
    Exportación de Servicios – Prestación de servicios de hospedaje
    No Domiciliado Factura, Boletas
    0203 Exportación de Servicios – Transporte de navieras Factura, Boletas
    0204
    Exportación de Servicios – Servicios a naves y aeronaves de
    bandera extranjera Factura, Boletas
    0205
    Exportación de Servicios - Servicios que conformen un Paquete
    Turístico Factura, Boletas
    0206
    Exportación de Servicios – Servicios complementarios al
    transporte de carga Factura, Boletas
    0207
    Exportación de Servicios – Suministro de energía eléctrica a
    favor de sujetos domiciliados en ZED Factura, Boletas
    0208
    Exportación de Servicios – Prestación servicios realizados
    parcialmente en el extranjero Factura, Boletas
    0301
    Operaciones con Carta de porte aéreo (emitidas en el ámbito
    nacional) Factura, Boletas
    0302 Operaciones de Transporte ferroviario de pasajeros Factura, Boletas
    0303 Operaciones de Pago de regalía petrolera Factura, Boletas
    1001 Operación Sujeta a Detracción Factura, Boletas
    1002 Operación Sujeta a Detracción- Recursos Hidrobiológicos Factura, Boletas
    "tipoOperacion" => "0101",
     */
    /*
   {"ruc":"20611263300",
   "razonSocial":"INVERSIONES YARECH S.R.L.",
   "nombreComercial":"-",
   "telefonos":[],
   "tipo":"SOC.COM.RESPONS. LTDA",
   "estado":"ACTIVO","condicion":"HABIDO",
   "direccion":"AV. LAS FLORES MZA. 17 LOTE. 4 A.H.  FLORES",
   "departamento":"AREQUIPA",
   "provincia":"CARAVELI",
   "distrito":"CHALA",
   "fechaInscripcion":"2023-07-11T00:00:00.000Z",
   "sistEmsion":"COMPUTARIZADO",
   "sistContabilidad":"COMPUTARIZADO",
   "actExterior":"SIN ACTIVIDAD",
   "actEconomicas":["Principal    - 4752 - VENTA AL POR MENOR DE ART\u00cdCULOS DE FERRETER\u00cdA, PINTURAS Y PRODUCTOS DE VIDRIO EN COMERCIOS ESPECIALIZADOS"],
   "cpPago":["NINGUNO"],"
   sistElectronica":["FACTURA PORTAL                      DESDE 30\/06\/2024"],
   "fechaEmisorFe":"2024-06-30T00:00:00.000Z","cpeElectronico":["FACTURA (desde 30\/06\/2024)"],"fechaPle":null,"padrones":["NINGUNO"],"fechaBaja":null,"profesion":""}
   */
    //Catálogo No. 06: Códigos de tipos de documentos de identidad
    /*
    0 Doc.trib.no.dom.sin.ruc
    1 Doc. Nacional de identidad
    4 Carnet de extranjería
    6 Registro Único de contribuyentes
    7 Pasaporte
    A Ced. Diplomática de identidad
    B Documento identidad país residencia-no.d
    C Tax Identification Number - TIN – Doc Trib PP.NN
    D Identification Number - IN – Doc Trib PP. JJ
    E TAM- Tarjeta Andina de Migración
    "client" => [
        "tipoDoc" => "6",
        "numDoc" => 20000000001,
        "rznSocial" => "EMPRESA X"
    ],
    */
    //Catálogo No. 07: Códigos de tipo de afectación del IGV
    /*
    10 Gravado - Operación Onerosa
    11 Gravado – Retiro por premio
    12 Gravado – Retiro por donación
    13 Gravado – Retiro
    14 Gravado – Retiro por publicidad
    15 Gravado – Bonificaciones
    16 Gravado – Retiro por entrega a trabajadores
    17 Gravado – IVAP
    20 Exonerado - Operación Onerosa
    21 Exonerado – Transferencia Gratuita
    30 Inafecto - Operación Onerosa
    31 Inafecto – Retiro por Bonificación
    32 Inafecto – Retiro
    33 Inafecto – Retiro por Muestras Médicas
    34 Inafecto - Retiro por Convenio Colectivo
    35 Inafecto – Retiro por premio
    36 Inafecto - Retiro por publicidad
    40 Exportación de bienes o servicios
    "tipAfeIgv" => 10,
    */
}
