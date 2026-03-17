<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tbl_producto_variaciones', function (Blueprint $table) {
            $table->id('id_variacion');
            $table->unsignedBigInteger('id_producto');
            $table->string('vSKU')->unique();
            $table->string('vCodigo_barras')->nullable();
            $table->string('vNombre_variacion')->nullable();
            $table->decimal('dPrecio', 10, 2)->default(0.00);
            $table->decimal('dPrecio_adicional', 10, 2)->default(0.00);
            $table->integer('iStock_variacion')->default(0);
            $table->decimal('dPeso', 8, 2)->nullable()->comment('Peso en kg');
            $table->string('vClase_envio')->nullable();
            $table->text('tDescripcion')->nullable();
            $table->string('vImagen')->nullable();
            $table->boolean('bActivo')->default(true);
            $table->timestamp('tFecha_registro')->useCurrent();
            $table->timestamp('tFecha_actualizacion')->useCurrent()->onUpdate(now());
            
            $table->foreign('id_producto')
                  ->references('id_producto')
                  ->on('tbl_productos')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_producto_variaciones');
    }
};