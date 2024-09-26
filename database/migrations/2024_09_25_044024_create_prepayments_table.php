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
        Schema::create('prepayments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sale_id');  // ID de la venta
            $table->decimal('amount', 15, 2);  // Monto abonado
            $table->string('related_doc_type')->nullable();  // Tipo de documento relacionado (e.g., factura o boleta)
            $table->string('related_doc_number')->nullable();  // Número de documento relacionado
            $table->decimal('total', 15, 2)->nullable();  // Monto total del anticipo
            $table->string('xml_file')->nullable();  // XML del anticipo
            $table->string('signed_xml_file')->nullable();  // XML firmado del anticipo
            $table->string('cdr_file')->nullable();  // CDR del anticipo
            $table->string('document_path')->nullable();
            $table->string('file_path')->nullable();
            $table->timestamps();
            
            // Foreign key constraint
            $table->foreign('sale_id')->references('id')->on('sales')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prepayments');
    }
};
