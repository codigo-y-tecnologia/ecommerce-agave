<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tbl_atributos', function (Blueprint $table) {
            $table->id('id_atributo');
            $table->string('vNombre', 100); // Ej: "Tamaño", "Tipo", "Edad"
            $table->string('vSlug', 100)->unique(); // Para URLs amigables
            $table->text('tDescripcion')->nullable();
            $table->boolean('bActivo')->default(true);
            $table->timestamps(); // ESTA LÍNEA ES CRÍTICA
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_atributos');
    }
};