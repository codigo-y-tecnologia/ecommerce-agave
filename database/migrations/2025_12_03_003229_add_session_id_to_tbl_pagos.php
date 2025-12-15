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
        Schema::table('tbl_pagos', function (Blueprint $table) {
            $table->string('vSessionID')->nullable()->after('vReferencia');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_pagos', function (Blueprint $table) {
            $table->dropColumn('vSessionID');
        });
    }
};
