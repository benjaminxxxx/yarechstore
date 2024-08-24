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
        Schema::create('document_sunat_types', function (Blueprint $table) {
            $table->string('code', 2)->primary(); // Alfanumérico, ejemplo: '0', '1', 'A', etc.
            $table->string('name'); // Nombre completo del tipo de documento.
            $table->string('short_name'); // Nombre corto para mostrar en tablas.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_sunat_types');
    }
};
