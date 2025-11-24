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
        Schema::create('tbl_reembolsos', function (Blueprint $table) {
           
            $table->id('id_reembolso');                          
            $table->string('vNombre', 100);                      
            $table->enum('eTipo', ['COMPRA', 'GASTO', 'OTRO']);      
            $table->decimal('dMonto', 10, 2);                
            $table->tinyInteger('bActivo')->nullable()->default(1); 
            $table->timestamp('dFecha_creacion')->nullable()->useCurrent(); 

         
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_reembolsos');
    }
};