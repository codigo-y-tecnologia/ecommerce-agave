<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Superadmin\SuperadminController;
use App\Http\Controllers\Perfil\DireccionController;
use App\Models\Direccion;
use App\Http\Controllers\Perfil\PerfilController;
use App\Http\Controllers\Admin\AdminPerfilController;

Route::get('/', function () {
    // Si el usuario está autenticado, lo redirigimos a su dashboard según su rol
    if (Auth::check()) {
        $rol = Auth::user()->eRol;

        switch ($rol) {
            case 'cliente':
                return view('dashboards.cliente');
            case 'admin':
                return view('dashboards.admin');
            case 'superadmin':
                return view('dashboards.superadmin');
            default:
                return view('dashboards.cliente');
        }
    }

    // Si no está autenticado, muestra la vista pública
    return view('dashboards.cliente');
})->name('home');

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

// Rutas para clientes
// --------------------
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
