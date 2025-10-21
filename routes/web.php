<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\EtiquetaController; 
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\MarcaController; 

// Cambiar esta ruta para usar AuthController en lugar de la función anónima
Route::get('/', [AuthController::class, 'index'])->name('inicio');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/usuarios/crear', [AuthController::class, 'showRegister'])->name('usuarios.create');
Route::post('/usuarios', [AuthController::class, 'register'])->name('usuarios.store');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::resource('/categorias', CategoriaController::class);
Route::resource('productos', ProductoController::class);
Route::resource('marcas', MarcaController::class);
Route::resource('etiquetas', EtiquetaController::class);