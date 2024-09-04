<!-- resources/views/pdf/ticket.blade.php -->
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: 'Courier', monospace;
            font-size: 8pt;
            text-align: center;
            margin: 0;
            padding: 0;
        }

        .header {
            margin-bottom: 5px;
        }

        .header img {
            width: 60px;
            margin-bottom: 5px;
        }

        .header .company-name {
            font-weight: bold;
            text-transform: uppercase;
        }

        .header .ruc {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .header .address {
            word-wrap: break-word;
            margin-bottom: 5px;
        }

        .header .location {
            margin-bottom: 5px;
        }

        .title {
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 5px;
            margin-bottom: 5px
        }

        .title2 {
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 5px;
            margin-bottom: 5px;
            font-size: 34px;
            font-family: Arial, Helvetica, sans-serif
        }

        .details {
            text-align: left;
            margin-bottom: 5px;
        }

        .details table {
            width: 100%;
        }

        .details th,
        .details td {
            padding: 2px 0;
        }

        .details .line {
            border-bottom: 1px dotted black;
            margin: 5px 0;
        }

        .totals {
            text-align: left;
            margin-bottom: 5px;
        }

        .qr {
            text-align: center;
            margin-top: 5px;
            margin-bottom: 5px;
        }

        .footer {
            text-align: center;
            margin-top: 5px;
            font-size: 7pt;
        }

        table {
            width: 100%
        }

        @page {
            margin: 15px;
            /* Establecer márgenes a cero */
        }

        body {
            margin: 0;
            /* Eliminar margen del cuerpo */
        }

        .ticket {
            width: 100%;
            /* Ajustar el ancho al 100% del papel */
            /* Puedes agregar más estilos para personalizar tu boleta */
        }
    </style>
</head>

<body>
    <div class="header">
        <h1 class="title2">YARECH</h1>
        <div class="company-name">{{$company_name}}</div>
        <div class="ruc">RUC: {{$company_ruc}}</div>
        <div class="address">
            CENTRAL: {{$company_address}} - Tienda Chala
        </div>
    </div>

    <div class="title">{{$text_document}}</div>
    <div class="details">
        <table>
            <tr>
                <td>Fecha de Emisión: {{$date}}</td>
            </tr>
            <tr>
                <td>Cliente: {{$client['numDoc']}} - {{$client['rznSocial']}}</td>
            </tr>
        </table>
        <div class="line"></div>
        <table style="width:100%" cellspacing="5">
            <tr>
                <th>Cod</th>
                <th>Descripción</th>
                <th style="text-align:center">Cant.</th>
                <th style="text-align:right">P.Unit.</th>
                <th style="text-align:right">Importe</th>
            </tr>
            @foreach ($items as $item)
                <tr>
                    <td valign="TOP">{{ $item['code'] }}</td>
                    <td colspan="4">{{ $item['description'] }}</td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td style="text-align:center">{{ $item['quantity'] }}</td>
                    <td style="text-align:right; white-space: nowrap;">S/ {{ number_format($item['unit_price'], 2, '.', ',') }}</td>
                    <td style="text-align:right; white-space: nowrap;">S/ {{ number_format($item['total'], 2, '.', ',') }}</td>
                </tr>
            @endforeach
            <tfoot>
                <tr>
                    <td colspan="4" style="text-align:right">Op. Gravada:</td>
                    <td style="text-align:right; white-space: nowrap;">S/ {{ number_format((double) $op_gravada, 2, '.', ',') }}</td>
                </tr>
                <tr>
                    <td colspan="4" style="text-align:right">IGV:</td>
                    <td style="text-align:right; white-space: nowrap;">S/ {{ number_format((double) $igv, 2, '.', ',') }}</td>
                </tr>
                <tr>
                    <td colspan="4" style="text-align:right">Total Pagado:</td>
                    <td style="text-align:right; white-space: nowrap;">S/ {{ number_format((double) $total_amount, 2, '.', ',') }}</td>
                </tr>
                <tr>
                    <td colspan="100%">
                        <div class="line"></div>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="text-align:right">Efectivo Soles:</td>
                    <td style="text-align:right;  white-space: nowrap;">S/ {{ number_format((double) $total_pagado, 2, '.', ',') }}</td>
                </tr>
                <tr>
                    <td colspan="4" style="text-align:right">Vuelto:</td>
                    <td style="text-align:right; white-space: nowrap;">S/ {{ number_format((string) $vuelto, 2, '.', ',') }}</td>
                </tr>
            </tfoot>
        </table>
        <div class="line"></div>
    </div>


    <div class="qr">
        <!--<img src="{{ asset('path/to/qr.png') }}" alt="QR Code">-->
    </div>

    <div class="footer">
        Este es un documento emitido electrónicamente.<br>
        Guarde este documento para cualquier reclamo.<br>
        Gracias por su compra.
    </div>
</body>

</html>
