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
        Schema::create('presentations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); // Relación con productos
            $table->string('barcode')->nullable(); // Código de barras único
            $table->string('unit'); // Unidad de medida (ej: "bundle", "box", "unit")
            $table->string('description')->nullable(); // Descripción de la presentación
            $table->integer('factor'); // Factor multiplicador de unidades (ej: 500 para "Bundle of 500")
            $table->decimal('price', 10, 2); // Precio de esta presentación
            $table->timestamps(); // Timestamps de creación y actualización
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presentations');
    }
};
