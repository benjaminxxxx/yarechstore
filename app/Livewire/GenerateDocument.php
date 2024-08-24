<?php

namespace App\Livewire;

use Livewire\Component;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class GenerateDocument extends Component
{
    public $document;
    public function render()
    {
        return view('livewire.generate-document');
    }
    public function createDocument(){
        $items = [
            ['code' => '001', 'description' => 'Taladro Percutor 13mm 750W Bosch', 'quantity' => 1, 'unit_price' => 250.00, 'discount' => 0.00, 'total' => 250.00],
            ['code' => '002', 'description' => 'Cinta Métrica de Acero 5m Stanley', 'quantity' => 2, 'unit_price' => 35.00, 'discount' => 5.00, 'total' => 65.00],
            ['code' => '003', 'description' => 'Juego de Llaves Combinadas 6-22mm', 'quantity' => 1, 'unit_price' => 120.00, 'discount' => 10.00, 'total' => 110.00],
            ['code' => '004', 'description' => 'Escalera Multifuncional 4x3 12 Peldaños', 'quantity' => 1, 'unit_price' => 320.00, 'discount' => 0.00, 'total' => 320.00],
            ['code' => '005', 'description' => 'Caja de Herramientas Plástica 16” Pretul', 'quantity' => 1, 'unit_price' => 45.00, 'discount' => 0.00, 'total' => 45.00],
            ['code' => '006', 'description' => 'Paquete de 50 Tornillos para Madera 1½”', 'quantity' => 5, 'unit_price' => 2.00, 'discount' => 0.00, 'total' => 10.00],
        ];

        $data = [
            'items' => $items,
            'op_gravada' => 800.00,
            'igv' => 144.00,
            'total_pagado' => 944.00,
            'vuelto' => 56.00,
        ];


        // Renderizar la vista y generar el PDF
        $pdf = Pdf::loadView('documents.boleta', $data);

        $width = 80 / 25.4 * 72; // Convertir 80 mm a puntos
        $height = 200 / 25.4 * 72; // Longitud de 300 mm convertida a puntos (ajústala según la necesidad)
        $pdf->setPaper([0, 0, $width, $height], 'portrait');

        $filename = Carbon::now()->format('Y/m') . '/boleta_ticketera.pdf';

        Storage::disk('public')->put($filename, $pdf->output());
        $this->document = Storage::disk('public')->url($filename);
    }
}
