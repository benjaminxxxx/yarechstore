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
        Schema::create('site_configs', function (Blueprint $table) {
            $table->id();
            $table->string('site_name')->nullable(); // Nombre del sitio
            $table->string('site_description', 500)->nullable(); // Descripción del sitio
            $table->string('site_favicon')->nullable(); // Ruta del favicon
            $table->string('site_logo')->nullable(); // Logo principal
            $table->string('site_logo_contrast')->nullable(); // Logo contraste
            $table->string('site_logo_horizontal')->nullable(); // Logo horizontal
            $table->string('site_logo_vertical')->nullable(); // Logo vertical
            $table->string('site_language', 10)->default('es'); // Idioma del sitio
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_configs');
    }
};
