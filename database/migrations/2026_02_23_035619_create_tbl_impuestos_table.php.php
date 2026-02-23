<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('tbl_impuestos')) {
            Schema::create('tbl_impuestos', function (Blueprint $table) {
                $table->id('id_impuesto');
                $table->string('vNombre', 100)->unique();
                $table->enum('eTipo', ['IVA', 'IEPS', 'OTRO'])->default('IVA');
                $table->decimal('dPorcentaje', 5, 2);
                $table->text('tDescripcion')->nullable();
                $table->boolean('bActivo')->default(true);
                $table->timestamp('dFecha_creacion')->useCurrent();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_impuestos');
    }
};