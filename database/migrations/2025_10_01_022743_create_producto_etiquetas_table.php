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
        Schema::create('tbl_producto_etiquetas', function (Blueprint $table) {
            $table->foreignId('id_producto')->constrained('tbl_productos')->onDelete('cascade');
            $table->foreignId('id_etiqueta')->constrained('tbl_etiquetas')->onDelete('cascade');
            $table->primary(['id_producto', 'id_etiqueta']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_producto_etiquetas');
    }
};
