<?php
// database/migrations/2024_03_13_000001_create_tbl_favoritos_temporales_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tbl_favoritos_temporales', function (Blueprint $table) {
            $table->id('id_favorito_temporal');
            $table->string('session_id', 255);
            $table->unsignedBigInteger('id_producto');
            $table->unsignedBigInteger('id_variacion')->nullable();
            $table->timestamp('tFecha_agregado')->useCurrent();
            
            $table->index('session_id');
            $table->unique(['session_id', 'id_producto', 'id_variacion'], 'uniq_temp_producto_variacion');
            
            $table->foreign('id_producto')
                  ->references('id_producto')
                  ->on('tbl_productos')
                  ->onDelete('cascade');
                  
            $table->foreign('id_variacion')
                  ->references('id_variacion')
                  ->on('tbl_producto_variaciones')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_favoritos_temporales');
    }
};