<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('atributo_opcions', function (Blueprint $table) {
             $table->id('id_opcion');
            $table->foreignId('id_atributo')->constrained('tbl_atributos')->onDelete('cascade');
            $table->string('vValor', 100);
            $table->string('vEtiqueta', 100);
            $table->boolean('bPredeterminado')->default(false);
            $table->integer('iOrden')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_atributo_opciones');
    }
};
