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
        /**
         * 1️⃣ Eliminar índice UNIQUE incorrecto de vGuest_token
         */
        DB::statement("
            ALTER TABLE tbl_carritos
            DROP INDEX tbl_carritos_vguest_token_unique
        ");

        /**
         * 2️⃣ Eliminar índice UNIQUE anterior (por si existe con otro nombre)
         */
        DB::statement("
            ALTER TABLE tbl_carritos
            DROP INDEX uniq_usuario_carrito_activo
        ");

        /**
         * 3️⃣ Crear índice UNIQUE correcto para carrito activo
         */
        DB::statement("
            ALTER TABLE tbl_carritos
            ADD UNIQUE KEY uniq_carrito_activo (uActivo)
        ");

        /**
         * 4️⃣ Eliminar triggers existentes
         */
        DB::statement("DROP TRIGGER IF EXISTS trg_carrito_before_insert");
        DB::statement("DROP TRIGGER IF EXISTS trg_carrito_before_update");

        /**
         * 5️⃣ Crear trigger BEFORE INSERT corregido
         */
        DB::statement("
            CREATE TRIGGER trg_carrito_before_insert
            BEFORE INSERT ON tbl_carritos
            FOR EACH ROW
            BEGIN
                IF NEW.eEstado = 'activo' THEN
                    IF NEW.id_usuario IS NOT NULL THEN
                        SET NEW.uActivo = NEW.id_usuario;
                    ELSE
                        SET NEW.uActivo = CRC32(NEW.vGuest_token);
                    END IF;
                ELSE
                    SET NEW.uActivo = NULL;
                END IF;
            END
        ");

        /**
         * 6️⃣ Crear trigger BEFORE UPDATE corregido
         */
        DB::statement("
            CREATE TRIGGER trg_carrito_before_update
            BEFORE UPDATE ON tbl_carritos
            FOR EACH ROW
            BEGIN
                IF NEW.eEstado = 'activo' THEN
                    IF NEW.id_usuario IS NOT NULL THEN
                        SET NEW.uActivo = NEW.id_usuario;
                    ELSE
                        SET NEW.uActivo = CRC32(NEW.vGuest_token);
                    END IF;
                ELSE
                    SET NEW.uActivo = NULL;
                END IF;
            END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        /**
         * Rollback limpio
         */

        DB::statement("DROP TRIGGER IF EXISTS trg_carrito_before_insert");
        DB::statement("DROP TRIGGER IF EXISTS trg_carrito_before_update");

        DB::statement("
            ALTER TABLE tbl_carritos
            DROP INDEX uniq_carrito_activo
        ");

        DB::statement("
            ALTER TABLE tbl_carritos
            ADD UNIQUE KEY tbl_carritos_vguest_token_unique (vGuest_token)
        ");
    }
};
