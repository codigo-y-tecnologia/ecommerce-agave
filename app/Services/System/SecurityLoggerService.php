<?php

namespace App\Services\System;

use App\Models\SecurityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class SecurityLoggerService
{
    // ── Método principal ────────────────────────────────────
    public static function log(
        string $eventType,
        string $severity,
        string $category,
        string $description,
        array  $metadata = [],
        ?int   $userId = null
    ): SecurityLog {

        return SecurityLog::create([
            'user_id'     => $userId ?? Auth::id(),
            'event_type'  => $eventType,
            'severity'    => $severity,
            'category'    => $category,
            'ip_address'  => Request::ip() ?? 'console',
            'user_agent'  => Request::userAgent(),
            'description' => $description,
            'metadata'    => $metadata,
        ]);
    }

    // ══════════════════════════════════════════════════════
    // 🔐 AUTH — Logins y sesiones
    // ══════════════════════════════════════════════════════

    public static function loginSuccess(int $userId, string $email): void
    {
        self::log(
            'login_success',
            'info',
            'auth',
            "Inicio de sesión exitoso: {$email}",
            ['email' => $email],
            $userId
        );
    }

    public static function loginFailed(string $email): void
    {
        self::log(
            'login_failed',
            'warning',
            'auth',
            "Intento de login fallido para: {$email}",
            ['email' => $email],
            null
        );
    }

    public static function logout(string $email): void
    {
        self::log(
            'logout',
            'info',
            'auth',
            "Cierre de sesión: {$email}",
            ['email' => $email]
        );
    }

    public static function passwordSetupEmailSent(int $userId, string $email): void
    {
        self::log(
            'password_setup_email_sent',
            'info',
            'auth',
            "Correo para establecer contraseña enviado a: {$email}",
            [
                'email' => $email
            ],
            $userId
        );
    }

    public static function passwordResetRequested(string $email): void
    {
        self::log(
            'password_reset_requested',
            'info',
            'auth',
            "Solicitud de restablecimiento de contraseña para: {$email}",
            [
                'email' => $email
            ]
        );
    }

    public static function passwordChanged(int $userId, string $email): void
    {
        self::log(
            'password_changed',
            'warning',
            'auth',
            "Contraseña cambiada para: {$email}",
            ['email' => $email],
            $userId
        );
    }

    public static function emailChangeCompleted(int $userId, string $oldEmail, string $newEmail): void
    {
        self::log(
            'email_change_completed',
            'warning',
            'auth',
            "Cambio de correo electrónico confirmado: {$oldEmail} → {$newEmail}",
            [
                'old_email' => $oldEmail,
                'new_email' => $newEmail
            ],
            $userId
        );
    }

    public static function emailChangeFailed(string $token): void
    {
        self::log(
            'email_change_failed',
            'warning',
            'auth',
            "Intento de verificación de cambio de email con token inválido",
            [
                'token' => $token
            ]
        );
    }

    public static function emailVerified(int $userId, string $email): void
    {
        self::log(
            'email_verified',
            'info',
            'auth',
            "Correo electrónico verificado: {$email}",
            [
                'email' => $email
            ],
            $userId
        );
    }

    public static function emailVerificationFailed(string $token): void
    {
        self::log(
            'email_verification_failed',
            'warning',
            'auth',
            "Intento de verificación con token inválido",
            [
                'token' => $token
            ]
        );
    }

    public static function bruteForceDetected(string $email, int $attempts): void
    {
        self::log(
            'brute_force_detected',
            'critical',
            'auth',
            "Posible fuerza bruta detectada en: {$email} ({$attempts} intentos)",
            ['email' => $email, 'attempts' => $attempts]
        );
    }

    public static function bruteForceIpDetected(string $ip, int $attempts): void
    {
        self::log(
            'brute_force_ip_detected',
            'critical',
            'auth',
            "Posible ataque de fuerza bruta desde IP: {$ip} ({$attempts} intentos)",
            [
                'ip' => $ip,
                'attempts' => $attempts
            ]
        );
    }

    // ══════════════════════════════════════════════════════
    // ⚙️ CONFIG — Cambios de configuración
    // ══════════════════════════════════════════════════════

    public static function configChanged(string $key, mixed $oldValue, mixed $newValue): void
    {
        self::log(
            'config_changed',
            'critical',
            'config',
            "Configuración modificada: {$key}",
            ['key' => $key, 'old' => $oldValue, 'new' => $newValue]
        );
    }

    // ══════════════════════════════════════════════════════
    // 🧑‍💼 ADMIN — Gestión de administradores y roles
    // ══════════════════════════════════════════════════════

    public static function adminCreated(string $name, string $email): void
    {
        self::log(
            'admin_created',
            'critical',
            'admin',
            "Nuevo administrador creado: {$name} ({$email})",
            ['name' => $name, 'email' => $email]
        );
    }

    public static function adminDeleted(string $name, string $email): void
    {
        self::log(
            'admin_deleted',
            'critical',
            'admin',
            "Administrador eliminado: {$name} ({$email})",
            ['name' => $name, 'email' => $email]
        );
    }

    public static function roleChanged(string $targetName, string $oldRole, string $newRole): void
    {
        self::log(
            'role_changed',
            'critical',
            'admin',
            "Rol modificado para {$targetName}: {$oldRole} → {$newRole}",
            ['user' => $targetName, 'old_role' => $oldRole, 'new_role' => $newRole]
        );
    }

    public static function permissionChanged(string $targetName, array $changes): void
    {
        self::log(
            'permission_changed',
            'critical',
            'admin',
            "Permisos modificados para: {$targetName}",
            ['user' => $targetName, 'changes' => $changes]
        );
    }

    // ══════════════════════════════════════════════════════
    // 🛒 ORDERS — Operaciones críticas de pedidos/pagos
    // ══════════════════════════════════════════════════════

    public static function orderRefunded(int $orderId, float $amount, string $reason): void
    {
        self::log(
            'order_refunded',
            'warning',
            'orders',
            "Reembolso manual de \${$amount} en pedido #{$orderId}",
            ['order_id' => $orderId, 'amount' => $amount, 'reason' => $reason]
        );
    }

    public static function orderCancelledByAdmin(int $orderId, string $reason): void
    {
        self::log(
            'order_cancelled_admin',
            'warning',
            'orders',
            "Pedido #{$orderId} cancelado manualmente por admin",
            ['order_id' => $orderId, 'reason' => $reason]
        );
    }

    public static function massivePriceChange(int $affectedProducts, string $description): void
    {
        self::log(
            'massive_price_change',
            'critical',
            'orders',
            "Cambio masivo de precios: {$affectedProducts} productos afectados",
            ['affected' => $affectedProducts, 'description' => $description]
        );
    }
}
