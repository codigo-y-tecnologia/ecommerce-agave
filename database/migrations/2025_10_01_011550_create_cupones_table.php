<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('tbl_cupones', function (Blueprint $table) {
            $table->id('id_cupon');  
            $table->string('vCodigo_cupon', 50)->unique();
            $table->decimal('dDescuento', 8, 2);
            $table->enum('eTipo', ['porcentaje', 'monto']);
            $table->date('dValido_desde');
            $table->date('dValido_hasta');
            $table->integer('iUso_maximo')->default(1);
            $table->boolean('bActivo')->default(true);
            $table->decimal('dMonto_minimo', 8, 2)->nullable()->after('dDescuento');
            $table->timestamps(); 
        });
    }

    public function down(): void
    {
  
        Schema::table('tbl_cupones', function (Blueprint $table) {
            $table->dropColumn('dMonto_minimo');
        });
    }
};
