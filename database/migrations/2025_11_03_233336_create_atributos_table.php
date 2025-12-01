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
        Schema::create('atributos', function (Blueprint $table) {
           $table->id('id_atributo');
            $table->string('vNombre', 100);
            $table->text('tDescripcion')->nullable();
            $table->enum('eTipo', ['texto', 'textarea', 'select', 'radio', 'checkbox', 'archivo'])->default('texto');
            $table->string('vLabel', 100)->nullable();
            $table->string('vPlaceholder', 100)->nullable();
            $table->boolean('bRequerido')->default(false);
            $table->integer('iOrden')->default(0);
            $table->boolean('bActivo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_atributos');
    }
};
