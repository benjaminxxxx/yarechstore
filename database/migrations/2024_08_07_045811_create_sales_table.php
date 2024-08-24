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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('code', 15)->unique();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('customer_document')->nullable();
            $table->unsignedBigInteger('customer_document_type')->nullable();
            $table->enum('status', ['cart', 'paid', 'canceled','debt'])->default('cart');
            $table->decimal('subtotal', 10, 2)->nullable();
            $table->decimal('total_amount', 10, 2)->nullable();
            $table->decimal('total_payed', 10, 2)->nullable();
            $table->decimal('igv', 10, 2)->nullable();
            $table->decimal('cash', 10, 2)->nullable();
            $table->unsignedInteger('document_status')->default(0);

            $table->string('document_code')->nullable(); // Código del documento, por ejemplo, B001
            $table->string('document_correlative')->nullable(); // Correlativo del documento, por ejemplo, 12345678
            $table->text('xml_path')->nullable(); // Ruta al archivo XML generado
            $table->text('signed_xml_path')->nullable(); // Ruta al archivo XML firmado
            $table->text('cdr_path')->nullable(); // Ruta al archivo CDR aprobado
            $table->text('document_path')->nullable(); // Ruta al archivo del voucher de pago
            $table->unsignedBigInteger('invoice_type_id')->nullable();
            $table->unsignedBigInteger('cash_register_id')->nullable();

            $table->timestamps();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('set null');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
            $table->foreign('invoice_type_id')->references('id')->on('invoices_type')->onDelete('restrict');
            $table->foreign('cash_register_id')->references('id')->on('cash_registers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
