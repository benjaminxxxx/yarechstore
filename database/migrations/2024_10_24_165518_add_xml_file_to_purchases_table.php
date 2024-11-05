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
        Schema::table('purchases', function (Blueprint $table) {
            $table->decimal('tax_amount', 10, 2)->nullable();
            $table->decimal('sub_total', 10, 2)->nullable();
            $table->string('xml_file')->nullable(); // Cambia 'some_existing_column' por la columna actual después de la cual deseas agregar 'xml_file'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropColumn('tax_amount');
            $table->dropColumn('sub_total');
            $table->dropColumn('xml_file');
        });
    }
};
