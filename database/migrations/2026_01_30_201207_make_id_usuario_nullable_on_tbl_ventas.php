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
        Schema::table('tbl_ventas', function (Blueprint $table) {
            // 1. Eliminar FK usando el nombre REAL
            $table->dropForeign('tbl_ventas_ibfk_2');

            // 2. Hacer nullable la columna
            $table->unsignedBigInteger('id_usuario')->nullable()->change();

            // 3. Crear FK con ON DELETE SET NULL
            $table->foreign('id_usuario')
                ->references('id_usuario')
                ->on('tbl_usuarios')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_ventas', function (Blueprint $table) {
            // Eliminar FK SET NULL (Laravel ahora sí conoce el nombre)
            $table->dropForeign(['id_usuario']);

            // Volver a NOT NULL
            $table->unsignedBigInteger('id_usuario')->nullable(false)->change();

            // Restaurar FK original
            $table->foreign('id_usuario')
                ->references('id_usuario')
                ->on('tbl_usuarios')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
    }
};
