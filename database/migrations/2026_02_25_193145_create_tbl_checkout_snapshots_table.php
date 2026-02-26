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
        Schema::create('tbl_checkout_snapshots', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_carrito')
                ->unique()
                ->constrained('tbl_carritos', 'id_carrito')
                ->cascadeOnDelete();

            $table->decimal('subtotal', 10, 2);
            $table->decimal('impuestos', 10, 2);
            $table->decimal('envio', 10, 2);
            $table->decimal('descuento', 10, 2);
            $table->decimal('total_final', 10, 2);

            $table->string('payment_session')->nullable()->index();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_checkout_snapshots');
    }
};
