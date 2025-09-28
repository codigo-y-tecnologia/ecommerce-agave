<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\AuthController;


Route::get('/', function () {
    return view('inicio');
});


Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/usuarios/crear', [AuthController::class, 'showRegister'])->name('usuarios.create');
Route::post('/usuarios', [AuthController::class, 'register'])->name('usuarios.store');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
