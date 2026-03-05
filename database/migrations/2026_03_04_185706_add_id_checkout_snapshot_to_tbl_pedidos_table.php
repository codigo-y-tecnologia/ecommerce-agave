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
            $table->unsignedBigInteger('id_checkout_snapshot')
                ->nullable()
                ->after('id_pedido');

            $table->foreign('id_checkout_snapshot')
                ->references('id')
                ->on('tbl_checkout_snapshots')
                ->nullOnDelete();

            $table->index('id_checkout_snapshot');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_pedidos', function (Blueprint $table) {
            $table->dropForeign(['id_checkout_snapshot']);
            $table->dropIndex(['id_checkout_snapshot']);
            $table->dropColumn('id_checkout_snapshot');
        });
    }
};
