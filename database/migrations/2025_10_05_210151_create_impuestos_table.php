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
        Schema::create('tbl_impuestos', function (Blueprint $table) {
           
            $table->id('id_impuesto');                          
            $table->string('vNombre', 100);                      
            $table->enum('eTipo', ['IVA', 'IEPS', 'OTRO']);      
            $table->decimal('dPorcentaje', 5, 2);                
            $table->tinyInteger('bActivo')->nullable()->default(1); 
            $table->timestamp('dFecha_creacion')->nullable()->useCurrent(); 

         
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_impuestos');
    }
};
