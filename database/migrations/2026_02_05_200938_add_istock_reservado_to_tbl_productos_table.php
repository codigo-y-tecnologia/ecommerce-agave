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
        Schema::table('tbl_productos', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_productos', 'iStock_reservado')) {
                $table->integer('iStock_reservado')
                    ->default(0)
                    ->after('iStock')
                    ->comment('Stock temporal reservado en checkout');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_productos', function (Blueprint $table) {
            if (Schema::hasColumn('tbl_productos', 'iStock_reservado')) {
                $table->dropColumn('iStock_reservado');
            }
        });
    }
};
