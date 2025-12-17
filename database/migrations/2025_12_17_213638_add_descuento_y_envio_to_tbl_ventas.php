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
        Schema::table('tbl_ventas', function (Blueprint $table) {
            $table->decimal('dDescuento', 10, 2)
                ->default(0)
                ->after('dTotal');

            $table->decimal('dCosto_envio', 10, 2)
                ->default(0)
                ->after('dDescuento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_ventas', function (Blueprint $table) {
            $table->dropColumn(['dDescuento', 'dCosto_envio']);
        });
    }
};
