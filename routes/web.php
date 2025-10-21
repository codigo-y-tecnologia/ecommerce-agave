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
// Route::middleware(['auth', \App\Http\Middleware\CheckRole::class . ':cliente'])->group(function () {
//     Route::get('/carrito', [CarritoController::class, 'index'])->name('carrito.index');
//     Route::post('/carrito/{producto}', [CarritoController::class, 'store'])->name('carrito.store');
//     Route::put('/carrito/{detalle}', [CarritoController::class, 'update'])->name('carrito.update');
//     Route::delete('/carrito/{detalle}', [CarritoController::class, 'destroy'])->name('carrito.destroy');

//     Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
// });

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
});

// --------------------
// Rutas para superadmin
// --------------------
// Route::middleware(['auth', 'role:superadmin'])->group(function () {
//     Route::get('/superadmin/panel', function () {
//         return "Bienvenido al panel del superadmin 👑";
//     })->name('superadmin.panel');
// });
