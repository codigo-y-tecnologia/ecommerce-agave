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
use App\Http\Controllers\VariacionController;
use App\Http\Controllers\ImpuestoController;


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
// Ruta para crear valores de atributos rápidamente (DEBE IR ANTES de las rutas con parámetros)
Route::post('/atributos/{atributo}/valores-quick', [AtributoController::class, 'quickCreateValor'])->name('atributos.valores-quick');
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
// RUTAS PARA VARIACIONES - AHORA USANDO VariacionController
// =====================================================================
Route::prefix('variaciones')->name('variaciones.')->group(function () {
    // Listado de productos con variaciones
    Route::get('/', [VariacionController::class, 'index'])->name('index');
    
    // Ver variaciones de un producto específico
    Route::get('/producto/{id}', [VariacionController::class, 'show'])->name('show');
    
    // Crear nueva variación para un producto
    Route::get('/producto/{producto_id}/crear', [VariacionController::class, 'create'])->name('create');
    Route::post('/producto/{producto_id}', [VariacionController::class, 'store'])->name('store');
    
    // Editar variación existente
    Route::get('/producto/{producto_id}/editar/{variacion_id}', [VariacionController::class, 'edit'])->name('edit');
    Route::put('/producto/{producto_id}/{variacion_id}', [VariacionController::class, 'update'])->name('update');
    
    // Eliminar variación
    Route::delete('/producto/{producto_id}/{variacion_id}', [VariacionController::class, 'destroy'])->name('destroy');
});

// Ruta alternativa para el sidebar (desde la navegación principal)
Route::get('/productos/variaciones', [VariacionController::class, 'index'])->name('productos.variaciones');

// Ruta alternativa para crear variación desde productos
Route::get('/productos/{id}/crear-variacion', [VariacionController::class, 'create'])
    ->name('productos.crear-variacion');

// =====================================================================
// RUTAS ADICIONALES PARA GESTIÓN DE PRODUCTOS
// =====================================================================
// Ruta para ver las variaciones de un producto específico
Route::get('/productos/{id}/ver-variaciones', function($id) {
    return redirect()->route('variaciones.show', $id);
})->name('productos.ver-variaciones');

// Ruta para generar combinaciones automáticas de atributos
Route::get('/productos/{id}/generar-combinaciones-view', function($id) {
    return redirect()->route('productos.atributos', $id);
})->name('productos.generar-combinaciones-view');

// =====================================================================
// NUEVAS RUTAS PARA PANEL DE GESTIÓN (TIPO WORDPRESS) - SIN AUTENTICACIÓN
// =====================================================================
// Rutas para gestión rápida desde productos - PÚBLICAS
Route::post('/categorias/quick-create', [CategoriaController::class, 'quickCreate'])->name('categorias.quick-create');
Route::post('/marcas/quick-create', [MarcaController::class, 'quickCreate'])->name('marcas.quick-create');
Route::post('/etiquetas/quick-create', [EtiquetaController::class, 'quickCreate'])->name('etiquetas.quick-create');
Route::post('/atributos/quick-create', [AtributoController::class, 'quickCreate'])->name('atributos.quick-create');

// Rutas para obtener datos en formato JSON - PÚBLICAS
Route::get('/categorias/json', [CategoriaController::class, 'getJson'])->name('categorias.json');
Route::get('/marcas/json', [MarcaController::class, 'getJson'])->name('marcas.json');
Route::get('/etiquetas/json', [EtiquetaController::class, 'getJson'])->name('etiquetas.json');
Route::get('/atributos/json', [AtributoController::class, 'getJson'])->name('atributos.json');

// =====================================================================
// RUTAS PARA IMPUESTOS
// =====================================================================
Route::resource('impuestos', ImpuestoController::class);
Route::post('/impuestos/quick-create', [ImpuestoController::class, 'quickCreate'])->name('impuestos.quick-create');
Route::get('/impuestos/json', [ImpuestoController::class, 'getJson'])->name('impuestos.json');

// =====================================================================
// RUTAS PARA IMÁGENES DE VARIACIONES (NUEVAS)
// =====================================================================
Route::get('/variaciones/{id}/imagenes', [VariacionController::class, 'getImagenes'])->name('variaciones.imagenes');
Route::post('/variaciones/{id}/imagenes/upload', [VariacionController::class, 'uploadImagen'])->name('variaciones.imagenes.upload');
Route::delete('/variaciones/imagenes/{imagenId}', [VariacionController::class, 'deleteImagen'])->name('variaciones.imagenes.delete');

// =====================================================================
// RUTA PARA DASHBOARD
// =====================================================================
Route::get('/dashboard', function () {
    return redirect()->route('inicio.real');
})->name('dashboard');

// =====================================================================
// RUTAS DE FALLBACK (si ninguna ruta coincide)
// =====================================================================
Route::fallback(function () {
    return redirect()->route('inicio.real');
});