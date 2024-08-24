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
        Schema::create('cash_registers', function (Blueprint $table) {
            $table->id();
            $table->string('code', 15)->unique();
            $table->unsignedBigInteger('user_id')->nullable()->comment('User who opened the cash register');
            $table->decimal('initial_amount', 10, 2)->default(0)->comment('Initial amount in the cash register');
            $table->decimal('current_amount', 10, 2)->default(0)->comment('Current amount in the cash register');
            $table->timestamp('opened_at')->nullable()->comment('Date and time when the cash register was opened');
            $table->timestamp('closed_at')->nullable()->comment('Date and time when the cash register was closed');
            $table->string('status')->default('open')->comment('Status of the cash register (open/closed)');
            $table->timestamps();

            // Foreign key to reference the user who opened the cash register
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_registers');
    }
};
