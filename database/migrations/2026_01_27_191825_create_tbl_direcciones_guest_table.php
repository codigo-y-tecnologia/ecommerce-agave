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
        Schema::create('tbl_direcciones_guest', function (Blueprint $table) {
            $table->bigIncrements('id_direccion_guest');

            $table->char('vGuest_token', 36);
            $table->string('vNombre', 60);
            $table->string('vApaterno', 50);
            $table->string('vAmaterno', 50)->nullable();
            $table->string('vEmail', 100);
            $table->string('vTelefono_contacto', 20);
            $table->string('vRFC', 13)->nullable();

            $table->string('vCalle', 150);
            $table->string('vNumero_exterior', 20);
            $table->string('vNumero_interior', 20)->nullable();
            $table->string('vColonia', 150);
            $table->string('vCodigo_postal', 10);
            $table->string('vCiudad', 80);
            $table->string('vEstado', 80);

            $table->string('vEntre_calle_1', 150)->nullable();
            $table->string('vEntre_calle_2', 150)->nullable();
            $table->text('tReferencias')->nullable();

            $table->boolean('bDireccion_principal')->default(false);

            $table->timestamp('tFecha_registro')->useCurrent();

            // Índice
            $table->index('vGuest_token', 'idx_guest_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_direcciones_guest');
    }
};
