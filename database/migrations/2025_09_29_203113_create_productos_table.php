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
        Schema::create('tbl_productos', function (Blueprint $table) {
           $table->id('id_producto');
            $table->string('vCodigo_barras', 20)->unique();
            $table->string('vNombre', 100);
            $table->text('tDescripcion_corta')->nullable();
            $table->text('tDescripcion_larga')->nullable();
            $table->decimal('dPrecio_compra', 10, 2)->nullable();
            $table->decimal('dPrecio_venta', 10, 2);
            $table->unsignedInteger('iStock')->default(0);
            $table->foreignId('id_marca')->nullable()->constrained('tbl_marcas')->onDelete('set null');
            $table->foreignId('id_categoria')->nullable()->constrained('tbl_categorias')->onDelete('set null');
            $table->boolean('bActivo')->default(true);

            $table->decimal('dPeso', 8, 2)->nullable()->comment('Peso en kilogramos');
            $table->decimal('dLargo_cm', 6, 2)->nullable()->comment('Largo en centímetros');
            $table->decimal('dAncho_cm', 6, 2)->nullable()->comment('Ancho en centímetros');
            $table->decimal('dAlto_cm', 6, 2)->nullable()->comment('Alto en centímetros');
            $table->string('vClase_envio', 50)->nullable()->comment('Clase de envío: estandar, express, fragil, grandes_dimensiones');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_productos');
    }
};
