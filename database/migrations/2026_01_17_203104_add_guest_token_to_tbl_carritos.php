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
        Schema::table('tbl_carritos', function (Blueprint $table) {
            $table->char('vGuest_token', 36)
                ->nullable()
                ->unique()
                ->after('id_usuario');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_carritos', function (Blueprint $table) {
            $table->dropUnique(['vGuest_token']);
            $table->dropColumn('vGuest_token');
        });
    }
};
