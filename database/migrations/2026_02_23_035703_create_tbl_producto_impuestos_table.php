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
        if (!Schema::hasTable('tbl_producto_impuestos')) {
            Schema::create('tbl_producto_impuestos', function (Blueprint $table) {
                $table->id('id_producto_impuesto');
                $table->foreignId('id_producto')->constrained('tbl_productos', 'id_producto')->onDelete('cascade');
                $table->foreignId('id_impuesto')->constrained('tbl_impuestos', 'id_impuesto')->onDelete('cascade');
                $table->timestamps();
                
                $table->unique(['id_producto', 'id_impuesto'], 'unique_producto_impuesto');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_producto_impuestos');
    }
};