<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tbl_usuarios', function (Blueprint $table) {
            $table->id('id_usuario');
            $table->string('vNombre', 60);
            $table->string('vApaterno', 50);
            $table->string('vAmaterno', 50)->nullable();
            $table->string('vEmail')->unique();
            $table->string('vPassword');
            $table->date('dFecha_nacimiento');
            $table->enum('eRol', ['cliente', 'admin'])->default('cliente');
            $table->rememberToken();
            $table->string('api_token', 80)->unique()->nullable()->default(null);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_usuarios');
    }
};