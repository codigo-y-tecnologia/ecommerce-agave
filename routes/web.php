<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Carrito\CarritoController;
use App\Http\Controllers\CheckoutController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerificationController;
use App\Models\Producto;
use App\Http\Controllers\CuponesController;
use App\Http\Controllers\ImpuestosController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Superadmin\SuperadminController;
use App\Http\Controllers\Perfil\DireccionController;
use App\Models\Direccion;

Route::get('/', [DashboardController::class, 'index'])->name('home');

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
Route::middleware(['auth', \App\Http\Middleware\CheckRole::class . ':cliente'])->group(function () {

// Perfil
    Route::get('/perfil', function () {
        return view('perfil.index');
    })->name('perfil.index');

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

// --------------------
// Rutas de Carrito
// --------------------
    Route::get('/carrito', [CarritoController::class, 'index'])->name('carrito.index');
    Route::post('/carrito/{producto}', [CarritoController::class, 'store'])->name('carrito.store');
    Route::put('/carrito/{detalle}', [CarritoController::class, 'update'])->name('carrito.update');
    Route::delete('/carrito/{detalle}', [CarritoController::class, 'destroy'])->name('carrito.destroy');

// --------------------
// Rutas de Checkout
// --------------------
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
 
});

// --------------------
// Rutas para admin
// --------------------
Route::middleware(['auth', \App\Http\Middleware\CheckRole::class . ':admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return "Bienvenido al panel de administración 👨‍💻";
    })->name('admin.dashboard');

    Route::get('/admin/usuarios', [UsuarioController::class, 'index'])->name('admin.usuarios');
    Route::get('/admin/usuarios/{id}/edit', [UsuarioController::class, 'edit'])->name('admin.usuarios.edit');
    Route::put('/admin/usuarios/{id}', [UsuarioController::class, 'update'])->name('admin.usuarios.update');
    Route::delete('/admin/usuarios/{id}', [UsuarioController::class, 'destroy'])->name('admin.usuarios.destroy');

    Route::get('/cupones', [CuponesController::class, 'index'])->name('cupones.index');
    Route::get('/cupones/create', [CuponesController::class, 'create'])->name('cupones.create');
    Route::post('/cupones', [CuponesController::class, 'store'])->name('cupones.store');

    Route::resource('impuestos', ImpuestosController::class);
});


// --------------------
// Rutas para superadmin
// --------------------
Route::middleware(['auth', \App\Http\Middleware\CheckRole::class . ':superadmin'])->group(function () {
    Route::get('/superadmin/panel', function () {
        return "Bienvenido al panel del superadmin 👑";
    })->name('superadmin.panel');

    Route::get('/admins', [SuperadminController::class, 'index'])->name('superadmin.admins.index');
    Route::post('/admins/promote/{id}', [SuperadminController::class, 'promoteToAdmin'])->name('superadmin.admins.promote');
    Route::post('/admins/demote/{id}', [SuperadminController::class, 'demoteToClient'])->name('superadmin.admins.demote');
    Route::delete('/admins/{id}', [SuperadminController::class, 'destroy'])->name('superadmin.admins.destroy');

    Route::get('/superadmin/admins/create', [SuperadminController::class, 'create'])->name('superadmin.admins.create');
    Route::post('/superadmin/admins', [SuperadminController::class, 'store'])->name('superadmin.admins.store');
});
