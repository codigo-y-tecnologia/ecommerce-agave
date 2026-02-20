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
        Schema::table('tbl_stock_reservas', function (Blueprint $table) {
            // Elimina el índice UNIQUE de session_id
            $table->dropUnique(['session_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_stock_reservas', function (Blueprint $table) {
            // Vuelve a agregar el índice UNIQUE a session_id
            $table->unique('session_id');
        });
    }
};
