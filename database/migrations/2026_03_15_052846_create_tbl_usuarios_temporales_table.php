<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('tbl_usuarios_temporales')) {
            Schema::create('tbl_usuarios_temporales', function (Blueprint $table) {
                $table->id('id_temp_usuario');
                $table->string('session_id');
                $table->string('vToken', 100)->unique()->nullable();
                $table->timestamp('tFecha_creacion')->nullable()->useCurrent();
                $table->timestamp('tFecha_expiracion')->nullable();
                
                $table->index('session_id');
                $table->index('tFecha_expiracion');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('tbl_usuarios_temporales');
    }
};