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
            $table->foreignId('id_producto')->constrained('tbl_productos')->onDelete('cascade');
            $table->string('vSKU', 50)->unique();
            $table->string('vCodigo_barras', 50)->nullable();
            $table->decimal('dPrecio', 10, 2);
            $table->decimal('dPrecio_oferta', 10, 2)->nullable();
            $table->integer('iStock')->default(0);
            $table->decimal('dPeso', 8, 2)->nullable();
            $table->string('vClase_envio', 50)->nullable();
            $table->text('tDescripcion')->nullable();
            $table->string('vImagen')->nullable();
            $table->boolean('bActivo')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_producto_variaciones');
    }
};