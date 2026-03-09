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
        Schema::create('tbl_cart_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_carrito');
            $table->string('canal'); // email, push, etc
            $table->timestamp('sent_at');
            $table->timestamps();

            $table->foreign('id_carrito')
                ->references('id_carrito')
                ->on('tbl_carritos')
                ->onDelete('cascade');

            $table->index(['id_carrito', 'canal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_cart_notifications');
    }
};
