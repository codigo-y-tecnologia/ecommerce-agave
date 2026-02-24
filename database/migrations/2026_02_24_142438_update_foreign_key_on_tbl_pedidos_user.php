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
            // 1 Eliminar la foreign key actual
            $table->dropForeign('tbl_pedidos_ibfk_1');

            // 2 Crear nueva foreign key con SET NULL
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
        Schema::table('tbl_pedidos', function (Blueprint $table) {

            // Revertir: eliminar la nueva FK
            $table->dropForeign(['id_usuario']);

            // Restaurar la anterior con CASCADE
            $table->foreign('id_usuario')
                ->references('id_usuario')
                ->on('tbl_usuarios')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
    }
};
