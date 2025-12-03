<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tbl_favoritos', function (Blueprint $table) {
            $table->id('id_favorito');
            $table->unsignedBigInteger('id_usuario');
            $table->unsignedBigInteger('id_producto');
            $table->boolean('bNotificado_stock')->default(false);
            $table->boolean('bNotificado_descuento')->default(false);
            $table->timestamp('tFecha_agregado')->useCurrent();
            
            // Claves foráneas
            $table->foreign('id_usuario')
                  ->references('id_usuario')
                  ->on('tbl_usuarios')
                  ->onDelete('cascade');
                  
            $table->foreign('id_producto')
                  ->references('id_producto')
                  ->on('tbl_productos')
                  ->onDelete('cascade');
            
            // Clave única para evitar duplicados
            $table->unique(['id_usuario', 'id_producto']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_favoritos');
    }
};