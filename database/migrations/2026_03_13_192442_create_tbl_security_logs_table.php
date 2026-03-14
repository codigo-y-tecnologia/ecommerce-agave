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
        Schema::create('tbl_security_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')
                ->references('id_usuario')
                ->on('tbl_usuarios')
                ->nullOnDelete();
            $table->string('event_type', 60);        // login_failed, config_changed, etc.
            $table->enum('severity', ['info', 'warning', 'critical'])->default('info');
            $table->string('category', 40);           // auth, config, admin, orders
            $table->string('ip_address', 45);
            $table->text('user_agent')->nullable();
            $table->string('description');            // Texto legible para humanos
            $table->json('metadata')->nullable();     // Datos extra (payload, cambios, etc.)
            $table->string('country')->nullable();    // Geolocalización básica
            $table->boolean('is_resolved')->default(false);
            $table->timestamp('resolved_at')->nullable();
            $table->unsignedBigInteger('resolved_by')->nullable();
            $table->foreign('resolved_by')
                ->references('id_usuario')
                ->on('tbl_usuarios')
                ->nullOnDelete();
            $table->timestamps();

            // Índices para búsquedas rápidas
            $table->index(['event_type', 'created_at']);
            $table->index(['severity', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['ip_address', 'created_at']);
            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_security_logs');
    }
};
