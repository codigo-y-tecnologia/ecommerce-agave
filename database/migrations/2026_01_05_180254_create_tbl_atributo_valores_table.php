<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tbl_atributo_valores', function (Blueprint $table) {
            $table->id('id_atributo_valor');
            $table->foreignId('id_atributo')->constrained('tbl_atributos', 'id_atributo')->onDelete('cascade');
            $table->string('vValor', 100); // Ej: "750ml", "1 Litro", "Joven"
            $table->string('vSlug', 100); // Para URLs
            $table->decimal('dPrecio_extra', 10, 2)->default(0); // Precio adicional por este valor
            $table->integer('iStock')->default(0); // Stock específico para este valor
            $table->integer('iOrden')->default(0);
            $table->boolean('bActivo')->default(true);
            $table->timestamps();
            
            $table->unique(['id_atributo', 'vSlug']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_atributo_valores');
    }
};