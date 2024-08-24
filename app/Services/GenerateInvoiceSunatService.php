<?php

namespace App\Services;

use App\Models\InvoicesType;
use App\Models\Sale;
use App\Models\Company as ModelsCompany;
use App\Models\PaymentMethod;
use App\Models\DigitalTransaction;
use App\Models\Correlative;
use App\Services\SunatService;
use Illuminate\Support\Facades\Storage;
use Luecano\NumeroALetras\NumeroALetras;
use Greenter\Report\XmlUtils;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

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
                $numerocorrelativo = str_pad($this->sale->document_correlative, 5, '0', STR_PAD_LEFT);
                $text_document .= " {$this->sale->document_code} - {$numerocorrelativo}";
            }
        }

        return $text_document;
    }
    public function createSimpleVoucher()
    {

        $itemsFromSale = $this->sale->items;
        $items = [];

        foreach ($itemsFromSale as $item) {

            $subtotal = $item->subtotal;
            $igv = $item->igv; // Puedes calcularlo si es necesario: $subtotal * 0.18;

            // Calcular el total basado en el subtotal y el IGV
            $total = $subtotal + $igv;

            // Agregar el ítem al array de items con el formato deseado
            $items[] = [
                'code' => sprintf('P%03d', $item->product_id), // Generar código basado en el ID del producto
                'description' => $item->product_name, // Descripción del producto
                'quantity' => $item->quantity, // Cantidad del producto
                'unit_price' => (float) $item->product_price, // Precio unitario del producto
                'discount' => 0.00, // No hay descuento según los datos proporcionados
                'subtotal' => (float) $item->subtotal, // Subtotal del producto
                'igv' => (float) $item->igv, // IGV del producto
                'total' => (float) $total, // Total del producto
            ];
        }


        $data = [
            'text_document' => $this->generateDocumentTitle(),
            'date' => Date('d/m/Y'),
            'company_name' => $this->company->name,
            'company_ruc' => $this->company->ruc,
            'company_address' => $this->company->address,
            'items' => $items,
            'op_gravada' => $this->sale->subtotal,
            'igv' => $this->sale->igv,
            'total_amount' => $this->sale->total_amount,
            'total_pagado' => $this->getTotalPagado($this->sale->id),
            'vuelto' => $this->sale->cash,
        ];


        // Renderizar la vista y generar el PDF
        $pdf = Pdf::loadView('documents.boleta', $data);

        $width = 80 / 25.4 * 72; // Convertir 80 mm a puntos
        $height = 200 / 25.4 * 72; // Longitud de 300 mm convertida a puntos (ajústala según la necesidad)
        $pdf->setPaper([0, 0, $width, $height], 'portrait');


        $filename = $this->getFileName();

        Storage::disk('public')->put($filename, $pdf->output());
        $document_path = Storage::disk('public')->url($filename);
        $this->sale->document_path = $filename;
        $this->sale->save();
    }
    public function getFileName()
    {
        $idFormatted = 'V' . str_pad($this->sale->id, 5, '0', STR_PAD_LEFT);

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
    public function process(Sale $sale)
    {

        $response = [];
        $this->sale = $sale;

        $this->generateNextCorrelative();

        $fechaEmision = date('Y-m-d\TH:i:sP');

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
            /*
            original
            
            $data = [
                "ublVersion" => "2.1",
                "tipoDoc" => "01",
                "tipoOperacion" => "0101",
                "serie" => "F001",
                "correlativo" => "1",
                "fechaEmision" => "2023-07-25T00:00:00-05:00",
                "formaPago" => [
                    "moneda" => "PEN",
                    "tipo" => "Contado"
                ],
                "tipoMoneda" => "PEN",
                "company" => [
                    "ruc" => 20609278235,
                    "razonSocial" => "Coders Free S.A.C",
                    "nombreComercial" => "Coders Free",
                    "address" => [
                        "ubigueo" => "150101",
                        "departamento" => "LIMA",
                        "provincia" => "LIMA",
                        "distrito" => "LIMA",
                        "urbanizacion" => "-",
                        "direccion" => "Av. Villa Nueva 221",
                        "codLocal" => "0000"
                    ]
                ],
                "client" => [
                    "tipoDoc" => "6",
                    "numDoc" => 20000000001,
                    "rznSocial" => "EMPRESA X"
                ],
                "mtoOperGravadas" => 169.49, // (200 / 1.18) para obtener el subtotal sin IGV
                "mtoIGV" => 30.51, // 200 - 169.49 para obtener el IGV
                "totalImpuestos" => 30.51, // Monto total de impuestos (IGV en este caso)
                "valorVenta" => 169.49, // Valor de la venta sin IGV
                "subTotal" => 200.00, // Total de la venta con IGV incluido
                "mtoImpVenta" => 200.00, // Total de la venta con IGV incluido
                "details" => [
                    [
                        "tipAfeIgv" => 10,
                        "codProducto" => "P001",
                        "unidad" => "NIU",
                        "descripcion" => "PRODUCTO 1",
                        "cantidad" => 2,
                        "mtoValorUnitario" => 84.74, // (169.49 / 2) para obtener el valor unitario sin IGV
                        "mtoValorVenta" => 169.49, // Valor total de venta sin IGV
                        "mtoBaseIgv" => 169.49, // Base imponible para el IGV
                        "porcentajeIgv" => 18,
                        "igv" => 30.51, // (200 - 169.49) para obtener el IGV total
                        "totalImpuestos" => 30.51, // Monto total de impuestos (IGV en este caso)
                        "mtoPrecioUnitario" => 100.00 // Precio unitario con IGV incluido
                    ]
                ],
                "legends" => [
                    [
                        "code" => "1000",
                        "value" => "SON DOSCIENTOS TREINTA Y SEIS CON 00/100 SOLES"
                    ]
                ]
            ];
*/

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
                    "tipoDoc" => '03',
                    "numDoc" => '00000000',
                    "rznSocial" => 'Cliente Varios'
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

            // 

            /*
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

                "client"=> [
                    "tipoDoc"=> "6",
                    "numDoc"=> 20000000001,
                    "rznSocial"=> "EMPRESA X"
                ],

                "mtoOperGravadas" => $this->sale->subtotal,
                "mtoIGV" => $this->sale->igv,
                "totalImpuestos" => $this->sale->igv,
                "valorVenta" => $this->sale->subtotal,
                "subTotal" => $this->sale->total_amount,
                "mtoImpVenta" => $this->sale->total_amount,
                "details" => $details,
                "legends" => [
                    [
                        "code" => "1000",
                        "value" => ""
                    ]
                ]
            ];
            */
            /*
                        if($this->sale->customer_id!=null){
                            $data['client'] = [
                                "tipoDoc" => (string)$this->sale->customer_document_type,
                                "numDoc" => $this->sale->customer_document,
                                "rznSocial" => $this->sale->customer_name
                            ];
                        }
            */
            $this->setTotales($data);
            $this->setLegends($data);

            $sunat = new SunatService;
            $see = $sunat->getSee($this->company);

            $invoice = $sunat->getInvoice($data);

            $result = $see->send($invoice);

            $response['xml'] = $see->getFactory()->getLastXml();

            $response['hash'] = (new XmlUtils())->getHashSign($response['xml']);

            $response['sunatResponse'] = $sunat->sunatResponse($result);


            $documentCorrelative = str_pad($this->sale->document_correlative, 6, '0', STR_PAD_LEFT);
            $prenombreDocumento = $this->sale->customer_document . '-' . $this->sale->document_code . '-' . $documentCorrelative;

            if (isset($response['xml'])) {

                $idFormatted = 'XML_' . $prenombreDocumento;
                $signed_xml_filename = Carbon::now()->format('Y/m') . '/' . $idFormatted . '.xml';

                Storage::disk('public')->put($signed_xml_filename, $response['xml']);
                $this->sale->signed_xml_path = $signed_xml_filename;
                $this->sale->save();
            }

            if (isset($response['sunatResponse']['cdrZip'])) {

                $idFormatted = 'CDR_' . $prenombreDocumento;
                $filename = Carbon::now()->format('Y/m') . '/' . $idFormatted . '.zip';

                Storage::disk('public')->put($filename, base64_decode($response['sunatResponse']['cdrZip']));
                $this->sale->cdr_path = $filename;
                $this->sale->save();
            }

            if ($response['sunatResponse']['success'] == true) {
                $this->createSimpleVoucher();
            }
        } else {
            $this->createSimpleVoucher();
        }

        return $response;
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
