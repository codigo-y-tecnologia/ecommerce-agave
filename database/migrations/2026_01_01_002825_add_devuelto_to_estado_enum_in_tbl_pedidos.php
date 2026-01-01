<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
            ALTER TABLE tbl_pedidos 
            MODIFY eEstado ENUM(
                'pendiente',
                'pagado',
                'enviado',
                'entregado',
                'cancelado',
                'devuelto'
            ) 
            DEFAULT 'pendiente'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("
            ALTER TABLE tbl_pedidos 
            MODIFY eEstado ENUM(
                'pendiente',
                'pagado',
                'enviado',
                'entregado',
                'cancelado'
            ) 
            DEFAULT 'pendiente'
        ");
    }
};
