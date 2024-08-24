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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('fullname');
            $table->string('document_type_id', 2); // Cambio aquí para permitir letras y números
            $table->string('document_number')->unique();
            $table->string('phone')->nullable(); // Cambiar 'phone_mobile' a 'phone'
            $table->string('address')->nullable();
            $table->string('department')->nullable();
            $table->string('province')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('district')->nullable(); // Suponiendo que es un código
            $table->foreignId('customer_type_id')->constrained('customer_types');
            $table->string('commercial_name')->nullable();
            $table->string('billing_ruc')->nullable()->unique();
            $table->string('billing_address')->nullable();
            $table->integer('points')->default(0);
            $table->text('notes')->nullable();
            $table->foreign('document_type_id')->references('code')->on('document_sunat_types');
        
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
