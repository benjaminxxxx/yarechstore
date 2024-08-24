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
        Schema::create('correlatives', function (Blueprint $table) {
            $table->id();
            $table->string('series', 4); // Por ejemplo: B001, F001
            $table->unsignedBigInteger('branch_id'); // Referencia a la sucursal
            $table->unsignedBigInteger('invoice_type_id'); // Referencia al tipo de documento
            $table->unsignedBigInteger('current_correlative')->default(1); // Correlativo actual
            $table->timestamps();

            // Relaciones
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->foreign('invoice_type_id')->references('id')->on('invoices_type')->onDelete('cascade');
            
            // Índices únicos
            $table->unique(['series', 'branch_id', 'invoice_type_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('correlatives');
    }
};
