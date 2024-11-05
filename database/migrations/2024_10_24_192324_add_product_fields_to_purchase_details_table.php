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
        Schema::table('purchase_details', function (Blueprint $table) {
            $table->string('product_name')->nullable();
            $table->string('product_identification')->nullable();
            $table->decimal('product_price', 10, 2)->nullable();
            $table->decimal('product_tax_amount', 10, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_details', function (Blueprint $table) {
            $table->dropColumn('product_name');
            $table->dropColumn('product_price');
            $table->dropColumn('product_identification');
            $table->dropColumn('product_tax_amount');
        });
    }
};
