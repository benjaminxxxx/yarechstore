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
        Schema::create('digital_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sale_id');
            $table->enum('method', ['Yape', 'Plin', 'Other'])->comment('Digital payment method');
            $table->decimal('amount', 10, 2);
            $table->enum('type', ['payment', 'refund','expense'])->comment('Type of transaction');
            $table->string('description')->nullable();
            $table->timestamps();
            $table->foreign('sale_id')->references('id')->on('sales')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('digital_transactions');
    }
};
