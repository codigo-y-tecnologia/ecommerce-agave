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
        Schema::table('tbl_cupones', function (Blueprint $table) {

            $table->unsignedInteger('iUsos_actuales')->default(0)->after('iUso_maximo');

            $table->enum('eTipo', [
                'porcentaje',
                'monto',
                'envio_gratis'
            ])->default('porcentaje')->change();

            $table->decimal('dMonto_minimo', 10, 2)->nullable()->after('dDescuento');

            $table->unsignedInteger('iUsos_por_usuario')->nullable()->after('iUsos_actuales');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_cupones', function (Blueprint $table) {
            $table->dropColumn('iUsos_actuales');
            $table->enum('eTipo', [
                'porcentaje',
                'monto'
            ])->default('porcentaje')->change();
            $table->dropColumn('dMonto_minimo');
            $table->dropColumn('iUsos_por_usuario');
        });
    }
};
