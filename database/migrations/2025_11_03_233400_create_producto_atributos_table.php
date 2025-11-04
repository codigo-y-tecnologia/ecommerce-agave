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
       Schema::create('tbl_producto_atributos', function (Blueprint $table) {
            $table->foreignId('id_producto')->constrained('tbl_productos')->onDelete('cascade');
            $table->foreignId('id_atributo')->constrained('tbl_atributos')->onDelete('cascade');
            $table->text('vValor')->nullable();
            $table->foreignId('id_opcion')->nullable()->constrained('tbl_atributo_opciones')->onDelete('set null');
            $table->primary(['id_producto', 'id_atributo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_producto_atributos');
    }
};
