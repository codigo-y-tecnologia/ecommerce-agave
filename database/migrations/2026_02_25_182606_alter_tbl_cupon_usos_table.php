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
        Schema::table('tbl_cupon_usos', function (Blueprint $table) {

            $table->unsignedBigInteger('id_usuario')
                ->nullable()
                ->after('id_venta');

            $table->string('guest_token', 36)
                ->nullable()
                ->after('id_usuario');

            $table->index('id_usuario');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_cupon_usos', function (Blueprint $table) {

            $table->dropIndex(['id_usuario']);

            $table->dropColumn([
                'id_usuario',
                'guest_token'
            ]);
        });
    }
};
