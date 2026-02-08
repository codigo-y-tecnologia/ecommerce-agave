<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Carrito\CarritoController;
use App\Http\Controllers\Checkout\CheckoutController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Direccion;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerificationController;
use App\Models\Producto;
use App\Http\Controllers\Checkout\PaymentController;
use App\Http\Controllers\Checkout\OrderReceivedController;
use App\Http\Controllers\Checkout\CheckoutSuccessController;
use App\Http\Controllers\Checkout\CheckoutErrorController;
use App\Http\Controllers\SoporteController;
use App\Http\Controllers\Checkout\PaypalSuccesController;
use App\Http\Controllers\Perfil\PerfilController;
use App\Http\Controllers\Superadmin\SuperadminPerfilController;
use App\Http\Controllers\Superadmin\SuperadminController;
use App\Http\Controllers\Superadmin\CambiarEmailController;
use App\Http\Controllers\Superadmin\SpatieRoleController;
use App\Http\Controllers\Superadmin\SpatiePermissionController;
use App\Http\Controllers\Superadmin\SpatieRolePermissionController;
use App\Http\Controllers\Superadmin\UsuarioRolController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Perfil\DireccionController;
use App\Http\Controllers\Admin\AdminPerfilController;
use App\Http\Controllers\Auth\EmailChangeController;

// Route::get('/', function () {
//     return view('inicio');
// })->name('home');

Route::get('/', function () {
    // Trae productos activos (evita traer todo si la tabla es grande)
    $productos = Producto::where('bActivo', 1)->orderBy('tFecha_registro', 'desc')->get();

    return view('inicio', compact('productos'));
})->name('home');

// Rutas públicas para el carrito de compras
Route::get('/carrito', [CarritoController::class, 'index'])->name('carrito.index');
Route::post('/carrito/{producto}', [CarritoController::class, 'store'])->name('carrito.store');
Route::put('/carrito/{detalle}', [CarritoController::class, 'update'])->name('carrito.update');
Route::delete('/carrito/{detalle}', [CarritoController::class, 'destroy'])->name('carrito.destroy');

// Rutas publicas para el checkout
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');

Route::get('/checkout/cancel', [CheckoutController::class, 'cancel'])->name('checkout.cancel');

Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

Route::post('/checkout/crear-direccion', [CheckoutController::class, 'crearDireccion'])
    ->name('checkout.crearDireccion');

Route::put('/checkout/actualizar-direccion/{id}', [CheckoutController::class, 'actualizarDireccion'])
    ->name('checkout.actualizarDireccion');

Route::get('/api/direccion/{id}', function ($id) {
    $direccion = Direccion::where('id_direccion', $id)
        ->where('id_usuario', Auth::user()->id_usuario)
        ->first();

    return response()->json([
        'success' => (bool) $direccion,
        'direccion' => $direccion
    ]);
});

// Para usuarios invitados (guest)
Route::get('/checkout/direccion-guest/{id}', [CheckoutController::class, 'getDireccionGuest'])
    ->name('checkout.direccion-guest');

Route::post('/cupon/aplicar', [CheckoutController::class, 'aplicarCupon'])->name('cupon.aplicar');

// Rutas de pago

// Stripe
Route::post('/payment/stripe-session', [PaymentController::class, 'createStripeSession'])->name('payment.stripe.session');

// Ruta pública para el Webhook de Stripe 
Route::post('/stripe/webhook', [PaymentController::class, 'stripeWebhook'])->name('webhook.stripe');

// Order Received
Route::get('/order-received/{id}', [OrderReceivedController::class, 'show'])
    ->name('order.received');

Route::get('/pedido/{id}/pdf', [OrderReceivedController::class, 'pdf'])
    ->name('pedido.pdf');

Route::get('/checkout/success', [CheckoutSuccessController::class, 'index'])
    ->name('checkout.success');

Route::get('/checkout/error', [CheckoutErrorController::class, 'index'])->name('checkout.error');

Route::get('/checkout/payment-refunded', function () {
    return view('checkout.payment-refunded');
})->name('checkout.payment-refunded');

// PayPal
Route::post('/payment/paypal-create', [PaymentController::class, 'createPaypalOrder'])->name('payment.paypal.create');
Route::post('/payment/paypal-capture', [PaymentController::class, 'capturePaypalOrder'])->name('payment.paypal.capture');

Route::get('/paypal/success', [PaypalSuccesController::class, 'index'])
    ->name('paypal.success');

Route::get('/pago-error', function () {
    return view('checkout.session-error');
})->name('session.error');

// Login y registro solo para invitados
Route::middleware('guest')->group(function () {

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/usuarios/crear', [AuthController::class, 'showRegister'])->name('usuarios.create');
    Route::post('/usuarios', [AuthController::class, 'register'])->name('usuarios.store');

    // Rutas para verificación de email
    Route::get('/verify-email/{token}', [AuthController::class, 'verifyEmail'])->name('verification.verify');
    Route::get('/resend-verification', function () {
        return view('auth.resend-verification');
    })->name('verification.resend-form');
    Route::post('/resend-verification', [AuthController::class, 'resendVerificationEmail'])->name('verification.resend');

    // Rutas para restablecimiento de contraseña
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])
        ->name('password.request');

    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])
        ->name('password.email');

    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])
        ->name('password.reset');

    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])
        ->name('password.update');
});

Route::middleware('auth')->group(function () {

    Route::get('/dashboard/cliente', [DashboardController::class, 'cliente'])->name('dashboard.cliente');
    Route::get('/dashboard/admin', [DashboardController::class, 'admin'])->name('dashboard.admin');
    Route::get('/dashboard/superadmin', [DashboardController::class, 'superadmin'])->name('dashboard.superadmin');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// --------------------
// Rutas para clientes
// --------------------
Route::middleware(['auth', 'permission:gestionar_perfil'])->group(function () {

    // Módulo de perfil
    Route::get('/perfil', function () {
        return view('perfil.index');
    })->name('perfil.index');

    // Configuración de perfil
    Route::get('/perfil/configuracion', [PerfilController::class, 'configuracion'])->name('perfil.configuracion');

    Route::put('/perfil/actualizar', [PerfilController::class, 'actualizar'])->name('perfil.actualizar');

    Route::put('/perfil/cambiar-password', [PerfilController::class, 'cambiarPassword'])->name('perfil.cambiarPassword');

    Route::post('/perfil/logout-others', [PerfilController::class, 'logoutOtherDevices'])
        ->name('perfil.logoutOthers');

    Route::get('/perfil/verify-email/{token}', [PerfilController::class, 'verifyNewEmail'])
        ->name('perfil.verifyEmail');

    Route::delete('/perfil/eliminar', [PerfilController::class, 'eliminar'])->name('perfil.eliminar');
});

Route::middleware(['auth', 'permission:gestionar_direcciones'])->group(function () {

    // Módulo de direcciones
    Route::get('/perfil/direcciones', [DireccionController::class, 'index'])->name('direcciones.index');
    Route::post('/perfil/direcciones', [DireccionController::class, 'store'])->name('direcciones.store');
    Route::put('/perfil/direcciones/{id}', [DireccionController::class, 'update'])->name('direcciones.update');
    Route::delete('/perfil/direcciones/{id}', [DireccionController::class, 'destroy'])->name('direcciones.destroy');

    // Obtener una dirección por ID (para editar)
    Route::get('/api/direccion/{id}', function ($id) {
        $direccion = Direccion::where('id_direccion', $id)
            ->where('id_usuario', Auth::user()->id_usuario)
            ->first();

        if (!$direccion) {
            return response()->json(['success' => false, 'message' => 'Dirección no encontrada']);
        }

        return response()->json(['success' => true, 'direccion' => $direccion]);
    });
});

Route::middleware(['auth', 'spatie.role:cliente'])->group(function () {

    // Rutas de Soporte
    Route::get('/soporte', [SoporteController::class, 'form'])
        ->name('soporte.form');

    Route::post('/soporte/enviar', [SoporteController::class, 'send'])
        ->name('soporte.send');
});

// --------------------
// Rutas para admin
// --------------------
Route::get('/admin/dashboard', function () {
    return "Bienvenido al panel de administración 👨‍💻";
})
    ->middleware(['auth', 'permission:gestionar_tienda'])
    ->name('admin.dashboard');

Route::middleware(['auth', 'permission:gestionar_clientes'])->group(function () {

    // Gestión de clientes
    Route::get('/admin/usuarios', [UsuarioController::class, 'index'])->name('admin.usuarios');
    Route::get('/admin/usuarios/{id}/edit', [UsuarioController::class, 'edit'])->name('admin.usuarios.edit');
    Route::put('/admin/usuarios/{id}', [UsuarioController::class, 'update'])->name('admin.usuarios.update');
    Route::delete('/admin/usuarios/{id}', [UsuarioController::class, 'destroy'])->name('admin.usuarios.destroy');
});

Route::middleware(['auth', 'permission:mi_perfil_admin'])->group(function () {

    // Perfil del admin
    Route::get('/perfil/admin', [AdminPerfilController::class, 'index'])
        ->name('admin.perfil.index');

    Route::put('/datos', [AdminPerfilController::class, 'updateDatos'])
        ->name('admin.perfil.datos');

    Route::put('/password', [AdminPerfilController::class, 'updatePassword'])
        ->name('admin.perfil.password');

    Route::post('/cerrar-sesiones', [AdminPerfilController::class, 'logoutOtherDevices'])
        ->name('admin.perfil.logout.others');

    Route::get('/admin/verificar-email/{token}', [AdminPerfilController::class, 'verifyNewEmail'])
        ->name('admin.email.verify');

    Route::get('/email/change/verify/{token}', [EmailChangeController::class, 'verify'])
        ->name('email.change.verify');
});

// Reportes
Route::get('/reportes', function () {
    return view('reportes.index');
})
    ->middleware('permission:ver_reportes')
    ->name('reportes.index');

// --------------------
// Rutas para superadmin
// --------------------
Route::get('/superadmin/panel', function () {
    return "Bienvenido al panel del superadmin 👑";
})
    ->middleware(['auth', 'permission:configurar_sistema'])
    ->name('superadmin.panel');

Route::middleware(['auth', 'permission:gestionar_administradores'])->group(function () {

    // Gestión de administradores
    Route::get('/admins', [SuperadminController::class, 'index'])->name('superadmin.admins.index');
    Route::get('/superadmin/admins/create', [SuperadminController::class, 'create'])->name('superadmin.admins.create');
    Route::post('/superadmin/admins', [SuperadminController::class, 'store'])->name('superadmin.admins.store');
    Route::post('/admins/promote/{id}', [SuperadminController::class, 'promoteToAdmin'])->name('superadmin.admins.promote');
    Route::post('/admins/demote/{id}', [SuperadminController::class, 'demoteToClient'])->name('superadmin.admins.demote');
    Route::delete('/admins/{id}', [SuperadminController::class, 'destroy'])->name('superadmin.admins.destroy');
});

Route::middleware([
    'auth',
    'spatie.role:superadmin'
])->prefix('superadmin')
    ->name('superadmin.')
    ->group(function () {

        Route::get('/perfil', [SuperadminPerfilController::class, 'index'])
            ->name('perfil.index');

        Route::put('/perfil/datos', [SuperadminPerfilController::class, 'updateDatos'])
            ->name('perfil.datos');

        Route::put('/perfil/password', [SuperadminPerfilController::class, 'updatePassword'])
            ->name('perfil.password');

        Route::post('/perfil/logout-others', [SuperadminPerfilController::class, 'logoutOtherDevices'])
            ->name('perfil.logoutOthers');

        Route::get('/perfil/verify-email/{token}', [SuperadminPerfilController::class, 'verifyNewEmail'])
            ->name('perfil.verifyEmail');

        Route::get('/email/change/verify/{token}', [CambiarEmailController::class, 'verify'])
            ->name('email.change.verify');
    });

Route::middleware(['auth', 'permission:gestionar_permisos'])->group(function () {

    // Gestión de roles y permisos
    Route::get('/superadmin/rolesypermisos', function () {
        return view('superadmin.roles.rolesypermisos');
    })->name('roles.permisos');

    // Roles
    Route::get('/superadmin/roles', [SpatieRoleController::class, 'index'])
        ->name('roles.index');

    Route::post('/superadmin/roles', [SpatieRoleController::class, 'store'])
        ->name('roles.store');

    Route::get('/superadmin/roles/create', [SpatieRoleController::class, 'create'])
        ->name('roles.create');

    Route::get('/superadmin/roles/{role}', [SpatieRoleController::class, 'edit'])->name('roles.edit');

    Route::put('/superadmin/roles/{role}', [SpatieRoleController::class, 'update'])->name('roles.update');

    Route::delete('/superadmin/roles/{role}', [SpatieRoleController::class, 'destroy'])
        ->name('roles.destroy');

    // Permisos
    Route::get('/superadmin/permissions', [SpatiePermissionController::class, 'index'])
        ->name('permissions.index');

    Route::get('/superadmin/permissions/create', [SpatiePermissionController::class, 'create'])
        ->name('permissions.create');

    Route::post('/superadmin/permissions', [SpatiePermissionController::class, 'store'])
        ->name('permissions.store');

    Route::resource('permissions', SpatiePermissionController::class)
        ->except(['show']);

    // Asignar permisos a roles
    Route::get('/superadmin/roles/{role}/permissions', [SpatieRolePermissionController::class, 'edit'])
        ->name('roles.permissions.edit');

    Route::post('/superadmin/roles/{role}/permissions', [SpatieRolePermissionController::class, 'update'])
        ->name('roles.permissions.update');

    // Asignar rol a usuario

    Route::get(
        'usuarios',
        [UsuarioRolController::class, 'index']
    )
        ->name('usuarios.index');

    Route::get(
        'usuarios/{usuario}/rol',
        [UsuarioRolController::class, 'edit']
    )
        ->name('usuarios.roles.edit');

    Route::put(
        'usuarios/{usuario}/rol',
        [UsuarioRolController::class, 'update']
    )
        ->name('usuarios.roles.update');
});
