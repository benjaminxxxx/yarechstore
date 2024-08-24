<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoices_type', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nombre del tipo de documento, e.g., 'Factura', 'Boleta', 'Nota de Crédito'
            $table->string('code', 2)->unique(); // Código SUNAT del documento
            $table->string('description')->nullable(); // Descripción opcional
            $table->timestamps();
        });

        DB::table('invoices_type')->insert([
            ['name' => 'Factura', 'code' => '01', 'description' => 'Factura emitida a clientes'],
            ['name' => 'Boleta de Venta', 'code' => '03', 'description' => 'Boleta de venta emitida a clientes'],
            ['name' => 'Nota de Crédito', 'code' => '07', 'description' => 'Nota de crédito que modifica una factura'],
            ['name' => 'Nota de Débito', 'code' => '08', 'description' => 'Nota de débito que modifica una factura'],
            ['name' => 'Guía de Remisión - Remitente', 'code' => '09', 'description' => 'Guía de remisión emitida por el remitente'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices_types');
    }
};
