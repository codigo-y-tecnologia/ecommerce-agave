<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Eliminar la tabla si existe para recrearla con la estructura correcta
        Schema::dropIfExists('tbl_atributo_valores');
        
        Schema::create('tbl_atributo_valores', function (Blueprint $table) {
            $table->id('id_atributo_valor');
            $table->foreignId('id_atributo')->constrained('tbl_atributos', 'id_atributo')->onDelete('cascade');
            $table->string('vValor', 100);
            $table->string('vSlug', 100);
            $table->decimal('dPrecio_extra', 10, 2)->default(0);
            $table->integer('iStock')->default(0);
            $table->integer('iOrden')->default(0);
            $table->boolean('bActivo')->default(true);
            $table->timestamps();
            
            // Índices únicos compuestos - CORREGIDO: Slug único por atributo, no global
            $table->unique(['id_atributo', 'vSlug'], 'unique_atributo_slug');
            $table->unique(['id_atributo', 'vValor'], 'unique_atributo_valor');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_atributo_valores');
    }
};