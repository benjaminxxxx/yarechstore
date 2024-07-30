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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('ruc');
            $table->string('address');
            $table->string('logo')->nullable();
            $table->string('sol_user')->nullable();
            $table->string('sol_pass')->nullable();
            $table->string('cert_path')->nullable();
            $table->string('client_id')->nullable();
            $table->string('client_secret')->nullable();
            $table->boolean('production')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
