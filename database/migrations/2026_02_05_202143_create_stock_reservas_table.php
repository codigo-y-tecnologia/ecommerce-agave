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
        Schema::create('tbl_stock_reservas', function (Blueprint $table) {

            $table->bigIncrements('id_stock_reserva');

            $table->unsignedBigInteger('id_producto');
            $table->unsignedBigInteger('id_carrito')->nullable();

            $table->string('session_id')->nullable()->unique();

            $table->unsignedInteger('cantidad');

            $table->timestamp('expires_at')->index()
                ->comment('Momento en que la reserva expira');

            $table->timestamps();

            $table->foreign('id_producto')
                ->references('id_producto')
                ->on('tbl_productos')
                ->cascadeOnDelete();

            $table->foreign('id_carrito')
                ->references('id_carrito')
                ->on('tbl_carritos')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_reservas');
    }
};
