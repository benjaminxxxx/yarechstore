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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->onDelete('cascade'); // Relación con la tabla sales
            $table->date('payment_date')->nullable(); // Fecha de pago
            $table->string('payment_method')->nullable();; // Método de pago
            $table->string('destination')->nullable();; // Destino del pago
            $table->decimal('amount', 10, 2); // Monto del pago
            $table->string('receipt_number')->nullable(); // Número de recibo
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
