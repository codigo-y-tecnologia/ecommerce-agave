<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tbl_variacion_atributos', function (Blueprint $table) {
            $table->id('id_variacion_atributo');
            $table->foreignId('id_variacion')->constrained('tbl_producto_variaciones', 'id_variacion');
            $table->foreignId('id_atributo')->constrained('tbl_atributos', 'id_atributo');
            $table->foreignId('id_atributo_valor')->constrained('tbl_atributo_valores', 'id_atributo_valor');
            $table->timestamps();
            
            $table->unique(['id_variacion', 'id_atributo']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_variacion_atributos');
    }
};