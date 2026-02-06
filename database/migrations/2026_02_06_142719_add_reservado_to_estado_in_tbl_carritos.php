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
        DB::statement("
            ALTER TABLE tbl_carritos
            MODIFY eEstado ENUM('activo','abandonado','convertido','reservado')
            DEFAULT 'activo'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_carritos', function (Blueprint $table) {
            DB::statement("
                    ALTER TABLE tbl_carritos
                    MODIFY eEstado ENUM('activo','abandonado','convertido')
                    DEFAULT 'activo'
                ");
        });
    }
};
