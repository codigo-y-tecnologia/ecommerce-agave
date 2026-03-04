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
        Schema::table('tbl_checkout_snapshots', function (Blueprint $table) {
            // 🔹 Impuestos congelados
            $table->json('impuestos_por_tipo')
                ->nullable()
                ->after('impuestos');

            $table->decimal('subtotal_con_impuestos', 12, 2)
                ->after('impuestos_por_tipo');

            // 🔹 Cupón congelado
            $table->string('cupon_codigo', 50)
                ->nullable()
                ->after('descuento');

            $table->string('cupon_tipo', 20)
                ->nullable()
                ->after('cupon_codigo');

            $table->decimal('cupon_valor', 10, 2)
                ->nullable()
                ->after('cupon_tipo');

            $table->decimal('cupon_monto_aplicado', 12, 2)
                ->nullable()
                ->after('cupon_valor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_checkout_snapshots', function (Blueprint $table) {
            $table->dropColumn([
                'impuestos_por_tipo',
                'subtotal_con_impuestos',
                'cupon_codigo',
                'cupon_tipo',
                'cupon_valor',
                'cupon_monto_aplicado',
            ]);
        });
    }
};
