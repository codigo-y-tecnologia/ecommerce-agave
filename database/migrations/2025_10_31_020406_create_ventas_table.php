<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_ventas', function (Blueprint $table) {
            $table->id('id_venta');
            $table->bigInteger('id_pedido')->unsigned();
            $table->bigInteger('id_usuario')->unsigned();
            $table->timestamp('fFecha_venta')->useCurrent();
            $table->decimal('dTotal', 10, 2);
            $table->enum('eMetodo_pago', ['stripe', 'tarjeta', 'transferencia']);
            $table->enum('eEstado', ['completada', 'devuelta', 'reembolsada', 'cancelada'])->default('completada');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_ventas');
    }
};