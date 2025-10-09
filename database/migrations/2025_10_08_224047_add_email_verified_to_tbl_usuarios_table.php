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
            $table->timestamp('email_verified_at')->nullable()->after('vEmail');
            $table->boolean('is_verified')->default(false)->after('email_verified_at');
            $table->string('verification_token', 100)->nullable()->after('is_verified');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_usuarios', function (Blueprint $table) {
            $table->dropColumn(['email_verified_at', 'is_verified', 'verification_token']);
        });
    }
};
