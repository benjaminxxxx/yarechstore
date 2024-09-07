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
        Schema::create('invoice_extra_information', function (Blueprint $table) {
            $table->id();
            $table->enum('type',['header','extra','footer']); // Texto que se ubica debajo de la dirección de empresa
            $table->string('name',255); // Leyendas adicionales como campo y valor
            $table->string('value',255); // Footer adicional
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_extra_information');
    }
};
