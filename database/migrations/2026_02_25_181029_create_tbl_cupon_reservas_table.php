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
        Schema::create('tbl_cupon_reservas', function (Blueprint $table) {

            $table->id('id_cupon_reserva');

            $table->foreignId('id_cupon')->constrained('tbl_cupones', 'id_cupon');

            $table->foreignId('id_carrito')->unique()->constrained('tbl_carritos', 'id_carrito');

            $table->string('session_id')->nullable();

            $table->timestamp('expires_at')->index();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_cupon_reservas');
    }
};
