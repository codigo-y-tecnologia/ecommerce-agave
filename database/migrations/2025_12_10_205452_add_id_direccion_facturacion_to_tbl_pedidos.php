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
        Schema::table('tbl_pedidos', function (Blueprint $table) {
            $table->unsignedBigInteger('id_direccion_facturacion')
                  ->nullable()
                  ->after('id_direccion');

            $table->foreign('id_direccion_facturacion', 'tbl_pedidos_facturacion_fk')
                  ->references('id_direccion')
                  ->on('tbl_direcciones')
                  ->onDelete('set null')
                  ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_pedidos', function (Blueprint $table) {
            $table->dropForeign('tbl_pedidos_facturacion_fk');
            $table->dropColumn('id_direccion_facturacion');
        });
    }
};
