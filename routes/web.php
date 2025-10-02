<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CuponesController;




Route::get('/', function () {
    return view('inicio');
});


Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/usuarios/crear', [AuthController::class, 'showRegister'])->name('usuarios.create');
Route::post('/usuarios', [AuthController::class, 'register'])->name('usuarios.store');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');



Route::get('/cupones', [CuponesController::class, 'index'])->name('cupones.index');
Route::get('/cupones/create', [CuponesController::class, 'create'])->name('cupones.create');
Route::post('/cupones', [CuponesController::class, 'store'])->name('cupones.store');

