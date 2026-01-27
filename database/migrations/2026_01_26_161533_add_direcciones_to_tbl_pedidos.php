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
        Schema::table('tbl_pedidos', function (Blueprint $table) {

            $table->unsignedBigInteger('id_usuario')->nullable()->change();

            /*
            |--------------------------------------------------------------------------
            | Datos del cliente
            |--------------------------------------------------------------------------
            */
            $table->string('vNombre', 60)->after('id_direccion_facturacion');
            $table->string('vApaterno', 50);
            $table->string('vAmaterno', 50)->nullable();
            $table->string('vEmail', 100);
            /*
            |--------------------------------------------------------------------------
            | Dirección de ENVÍO
            |--------------------------------------------------------------------------
            */
            $table->string('env_telefono_contacto', 20)->after('vEmail');
            $table->string('env_calle', 150)->nullable();
            $table->string('env_numero_exterior', 20)->nullable();
            $table->string('env_numero_interior', 20)->nullable();
            $table->string('env_colonia', 150)->nullable();
            $table->string('env_codigo_postal', 10)->nullable();
            $table->string('env_ciudad', 80)->nullable();
            $table->string('env_estado', 80)->nullable();
            $table->string('env_entre_calle_1', 150)->nullable();
            $table->string('env_entre_calle_2', 150)->nullable();
            $table->text('env_referencias')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Dirección de FACTURACIÓN
            |--------------------------------------------------------------------------
            */
            $table->string('fac_telefono_contacto', 20)->after('env_referencias');
            $table->string('fac_calle', 150)->nullable();
            $table->string('fac_numero_exterior', 20)->nullable();
            $table->string('fac_numero_interior', 20)->nullable();
            $table->string('fac_colonia', 150)->nullable();
            $table->string('fac_codigo_postal', 10)->nullable();
            $table->string('fac_ciudad', 80)->nullable();
            $table->string('fac_estado', 80)->nullable();
            $table->string('fac_entre_calle_1', 150)->nullable();
            $table->string('fac_entre_calle_2', 150)->nullable();
            $table->text('fac_referencias')->nullable();

            /*
            |--------------------------------------------------------------------------
            | RFC (Facturación)
            |--------------------------------------------------------------------------
            */
            $table->string('vRFC', 13)->nullable()->after('fac_referencias');
            $table->char('vGuest_token', 36)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_pedidos', function (Blueprint $table) {
            $table->dropColumn([
                'vNombre',
                'vApaterno',
                'vAmaterno',
                'vEmail',
                'env_telefono_contacto',
                'env_calle',
                'env_numero_exterior',
                'env_numero_interior',
                'env_colonia',
                'env_codigo_postal',
                'env_ciudad',
                'env_estado',
                'env_entre_calle_1',
                'env_entre_calle_2',
                'env_referencias',
                'fac_telefono_contacto',
                'fac_calle',
                'fac_numero_exterior',
                'fac_numero_interior',
                'fac_colonia',
                'fac_codigo_postal',
                'fac_ciudad',
                'fac_estado',
                'fac_entre_calle_1',
                'fac_entre_calle_2',
                'fac_referencias',
                'vRFC',
                'vGuest_token'
            ]);

            $table->unsignedBigInteger('id_usuario')->nullable(false)->change();
        });
    }
};
