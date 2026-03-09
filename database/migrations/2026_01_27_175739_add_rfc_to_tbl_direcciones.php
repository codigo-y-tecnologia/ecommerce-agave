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
        Schema::table('tbl_direcciones', function (Blueprint $table) {
            $table->string('vRFC', 13)->nullable()->after('vTelefono_contacto');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_direcciones', function (Blueprint $table) {
            $table->dropColumn('vRFC');
        });
    }
};
