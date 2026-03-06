<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_cupon_usos', function (Blueprint $table) {
            $table->id('id_cupon');
            $table->unsignedBigInteger('id_venta');
            $table->unsignedBigInteger('id_usuario')->nullable();
            $table->string('guest_token', 36)->nullable();
            $table->timestamp('tFecha_uso')->useCurrent();

            $table->foreign('id_venta')->references('id_venta')->on('tbl_ventas')->onDelete('cascade');
            $table->foreign('id_usuario')->references('id_usuario')->on('tbl_usuarios')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_cupon_usos');
    }
};