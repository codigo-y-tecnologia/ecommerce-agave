<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        
        
        // Crear la tabla con la estructura correcta
        Schema::create('tbl_producto_atributos', function (Blueprint $table) {
            $table->id('id_producto_atributo');
            $table->foreignId('id_producto')
                  ->constrained('tbl_productos', 'id_producto')
                  ->onDelete('cascade');
            $table->foreignId('id_atributo')
                  ->constrained('tbl_atributos', 'id_atributo')
                  ->onDelete('cascade');
            $table->foreignId('id_atributo_valor')
                  ->constrained('tbl_atributo_valores', 'id_atributo_valor')
                  ->onDelete('cascade');
            $table->decimal('dPrecio_extra', 10, 2)->default(0);
            $table->timestamps();
            
            // Índice único para evitar duplicados
            $table->unique(['id_producto', 'id_atributo', 'id_atributo_valor'], 'unique_producto_atributo_valor');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_producto_atributos');
    }
};