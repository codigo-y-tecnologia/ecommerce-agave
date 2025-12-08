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
        Schema::table('tbl_usuarios', function (Blueprint $table) {
            // Solo agregar si no existen (para evitar errores en tu caso)
            if (!Schema::hasColumn('tbl_usuarios', 'remember_token')) {
                $table->string('remember_token', 100)->nullable()->after('vPassword');
            }

            if (!Schema::hasColumn('tbl_usuarios', 'api_token')) {
                $table->string('api_token', 100)->nullable()->unique()->after('remember_token');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_usuarios', function (Blueprint $table) {
            if (Schema::hasColumn('tbl_usuarios', 'api_token')) {
                $table->dropColumn('api_token');
            }

            if (Schema::hasColumn('tbl_usuarios', 'remember_token')) {
                $table->dropColumn('remember_token');
            }
        });
    }
};
