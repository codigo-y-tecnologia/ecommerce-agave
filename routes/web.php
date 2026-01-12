<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\EtiquetaController; 
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\MarcaController; 
use App\Http\Controllers\BusquedaController;
use App\Http\Controllers\FavoritoController;
use App\Http\Controllers\AtributoController;
use App\Http\Controllers\ValoracionController;

// RUTA PRINCIPAL - redirige a la página de inicio real
Route::get('/', function() {
    return redirect()->route('inicio.real');
})->name('inicio');

// Ruta para la página de inicio que muestra productos destacados
Route::get('/inicio-real', [BusquedaController::class, 'inicio'])->name('inicio.real');

// =====================================================================
// RUTAS DE AUTENTICACIÓN - Usando AuthController
// =====================================================================
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rutas de registro
Route::get('/registro', [AuthController::class, 'showRegister'])->name('usuarios.create');
Route::post('/registro', [AuthController::class, 'register'])->name('usuarios.store');

// =====================================================================
// RUTAS PÚBLICAS
// =====================================================================
Route::resource('/categorias', CategoriaController::class);
Route::resource('productos', ProductoController::class);
Route::resource('marcas', MarcaController::class);
Route::resource('etiquetas', EtiquetaController::class);
Route::resource('atributos', AtributoController::class);

// Rutas públicas para productos
Route::get('/producto/{id}', [ProductoController::class, 'showPublic'])->name('productos.show.public');
Route::get('/catalogo', [ProductoController::class, 'catalogo'])->name('productos.catalogo');

// =====================================================================
// RUTAS DE BÚSQUEDA
// =====================================================================
Route::get('/buscar', [BusquedaController::class, 'buscar'])->name('busqueda.resultados');
Route::get('/busqueda-rapida', [BusquedaController::class, 'busquedaRapida'])->name('busqueda.rapida');
Route::get('/buscar-productos', [BusquedaController::class, 'buscarProductos'])->name('busqueda.productos');

// =====================================================================
// RUTAS PARA FAVORITOS
// =====================================================================
// RUTAS PÚBLICAS PARA VISUALIZAR FAVORITOS
Route::get('/favoritos', [FavoritoController::class, 'index'])
     ->name('favoritos.index');

// RUTAS PROTEGIDAS PARA ACCIONES DE FAVORITOS
Route::middleware('auth')->group(function () {
    Route::post('/favoritos/toggle/{producto}', [FavoritoController::class, 'toggle'])
         ->name('favoritos.toggle');
    Route::delete('/favoritos/{producto}', [FavoritoController::class, 'destroy'])
         ->name('favoritos.destroy');
    Route::get('/favoritos/sync', [FavoritoController::class, 'sync'])
         ->name('favoritos.sync');
});

// =====================================================================
// RUTAS PARA ATRIBUTOS DE PRODUCTOS
// =====================================================================
Route::get('/productos/{id}/atributos', [ProductoController::class, 'atributos'])->name('productos.atributos');
Route::post('/productos/{id}/guardar-variaciones', [ProductoController::class, 'guardarVariaciones'])->name('productos.guardar-variaciones');
Route::post('/productos/{id}/generar-combinaciones', [ProductoController::class, 'generarCombinaciones'])->name('productos.generar-combinaciones');

// =====================================================================
// RUTAS PARA VALORES DE ATRIBUTOS
// =====================================================================
Route::prefix('atributos/{atributo}')->name('atributos.')->group(function () {
    Route::get('/valores', [AtributoController::class, 'valores'])->name('valores');
    Route::get('/valores/create', [AtributoController::class, 'createValor'])->name('valores.create');
    Route::post('/valores', [AtributoController::class, 'storeValor'])->name('valores.store');
    Route::get('/valores/{valor}/edit', [AtributoController::class, 'editValor'])->name('valores.edit');
    Route::put('/valores/{valor}', [AtributoController::class, 'updateValor'])->name('valores.update');
    Route::delete('/valores/{valor}', [AtributoController::class, 'destroyValor'])->name('valores.destroy');
});

// =====================================================================
// RUTAS PARA ASIGNAR ATRIBUTOS A PRODUCTOS
// =====================================================================
Route::get('/productos/{id}/asignar-atributos', [ProductoController::class, 'asignarAtributos'])->name('productos.asignar-atributos');
Route::post('/productos/{id}/guardar-atributos', [ProductoController::class, 'guardarAtributos'])->name('productos.guardar-atributos');

// =====================================================================
// RUTAS PARA VALORACIONES (VARIACIONES)
// =====================================================================
Route::prefix('valoraciones')->name('valoraciones.')->group(function () {
    // Listado de productos con valoraciones
    Route::get('/', [ValoracionController::class, 'index'])->name('index');
    
    // Ver valoraciones de un producto específico
    Route::get('/producto/{id}', [ValoracionController::class, 'show'])->name('show');
    
    // Crear nueva valoración para un producto
    Route::get('/producto/{producto_id}/crear', [ValoracionController::class, 'create'])->name('create');
    Route::post('/producto/{producto_id}', [ValoracionController::class, 'store'])->name('store');
    
    // Editar valoración existente
    Route::get('/producto/{producto_id}/editar/{variacion_id}', [ValoracionController::class, 'edit'])->name('edit');
    Route::put('/producto/{producto_id}/{variacion_id}', [ValoracionController::class, 'update'])->name('update');
    
    // Eliminar valoración
    Route::delete('/producto/{producto_id}/{variacion_id}', [ValoracionController::class, 'destroy'])->name('destroy');
});

// Ruta alternativa para el sidebar (desde la navegación principal)
Route::get('/productos/valoraciones', [ValoracionController::class, 'index'])->name('productos.valoraciones');

// Ruta alternativa para crear valoración desde productos
Route::get('/productos/{id}/crear-valoracion', [ValoracionController::class, 'create'])
    ->name('productos.crear-valoracion');

// =====================================================================
// RUTAS ADICIONALES PARA GESTIÓN DE PRODUCTOS
// =====================================================================
// Ruta para ver las valoraciones de un producto específico
Route::get('/productos/{id}/ver-valoraciones', function($id) {
    return redirect()->route('valoraciones.show', $id);
})->name('productos.ver-valoraciones');

// Ruta para generar combinaciones automáticas de atributos
Route::get('/productos/{id}/generar-combinaciones-view', function($id) {
    return redirect()->route('productos.atributos', $id);
})->name('productos.generar-combinaciones-view');

// =====================================================================
// RUTAS DE ADMINISTRACIÓN (si es necesario agregar más adelante)
// =====================================================================
// Ejemplo: Route::middleware(['auth', 'admin'])->group(function () {
//     // Rutas de administración aquí
// });

// =====================================================================
// RUTAS DE FALLBACK (si ninguna ruta coincide)
// =====================================================================
Route::fallback(function () {
    return redirect()->route('inicio.real');
});