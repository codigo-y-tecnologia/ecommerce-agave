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
        Schema::create('tbl_solicitudes_postventa', function (Blueprint $table) {

            $table->id('id_solicitud');

    $table->unsignedBigInteger('id_pedido');
    $table->unsignedBigInteger('id_usuario');

    $table->enum('eTipo', ['cancelacion', 'devolucion']);

    $table->enum('eEstado', [
        'pendiente',
        'aprobada',
        'rechazada',
        'reembolsada'
    ])->default('pendiente');

    $table->string('vMotivo', 255);
    $table->text('tRespuesta_admin')->nullable();

    $table->timestamps();

    $table->foreign('id_pedido')
          ->references('id_pedido')
          ->on('tbl_pedidos')
          ->onDelete('restrict')
          ->onUpdate('cascade');

    $table->foreign('id_usuario')
          ->references('id_usuario')
          ->on('tbl_usuarios')
          ->onDelete('restrict')
          ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_solicitudes_postventa');
    }
};
