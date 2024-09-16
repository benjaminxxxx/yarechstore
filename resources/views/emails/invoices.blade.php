<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Documentos de Factura - YARECH</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .container {
            width: 100%;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin: 20px auto;
            max-width: 600px;
        }

        .header {
            background-color: #0E7490;
            color: #ffffff;
            padding: 10px 0;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            text-transform: uppercase;
        }

        .content {
            padding: 20px;
        }

        .content p {
            margin: 10px 0;
            line-height: 1.6;
        }

        .content .highlight {
            color: #0E7490;
            font-weight: bold;
        }

        .footer {
            text-align: center;
            padding: 10px 0;
            color: #777;
            font-size: 12px;
            border-top: 1px solid #eaeaea;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>¡Gracias por su compra!</h1>
        </div>
        <div class="content">
            <p>Estimado Cliente,</p>
            <p>Gracias por realizar su compra en <strong>YARECH - Ferretería y Minería</strong>. Nos complace adjuntar
                los documentos solicitados relacionados con su compra.</p>
            <p>Documentos adjuntos:</p>
            <ul>
                @foreach ($data['info'] as $info)
                    <li><strong>{{ $info['name'] }}:</strong> {{ $info['description'] }}</li>
                @endforeach
            </ul>
            <p>Si tiene alguna pregunta, no dude en contactarnos. ¡Esperamos poder atenderle nuevamente!</p>
        </div>
        <div class="footer">
            <p>Saludos cordiales,<br>El equipo de YARECH - Ferretería y Minería</p>
        </div>
    </div>
</body>

</html>
