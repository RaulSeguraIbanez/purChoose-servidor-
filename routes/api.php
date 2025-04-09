<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\confPerfil;
use App\Http\Controllers\productosController;
use App\Http\Controllers\perfilPotatoController;
use App\Http\Controllers\categoriasController;
use App\Http\Controllers\opinionPotatoController;
use App\Http\Controllers\carritoController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

/*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
}); */


//Rutas para API

Route::post('/register', [AuthController::class, 'register']);
Route::post('/registerBusiness', [AuthController::class, 'registerBusiness']);
Route::post('/login', [AuthController::class, 'login']);

/*Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/perfil', function (Request $request) {
        return response()->json($request->user());
    });
}); */

Route::get('/productos', [productosController::class, 'indexProductos']);
Route::post('/productos', [productosController::class, 'storeProducto']);

Route::get('/productos/{id}/detalles', [productosController::class, 'showProductoDetallado']);
Route::post('/productos/{id}/upload-images', [productosController::class, 'storeImages']);

Route::get('/categorias', [categoriasController::class, 'indexCategorias']);
Route::post('/categorias', [categoriasController::class, 'storeCategoria']);
Route::post('/categorias/productos', [productosController::class, 'getProductosByCategorias']);

Route::apiResource('perfil', confPerfil::class);

Route::get('/opiniones/{product_id}', [opinionPotatoController::class, 'index']);
Route::post('/opiniones', [opinionPotatoController::class, 'store']);

// Listar items del carrito de un usuario
Route::get('/carrito/{user_id}', [CarritoController::class, 'index']);
Route::post('/carrito', [carritoController::class, 'store']);
Route::put('/carrito/{id}', [carritoController::class, 'update']);
Route::delete('/carrito/{id}', [carritoController::class, 'destroy']);

//Rutas para modificar datos del usuario y mostrarlos. Solo permite modificar contraseña y ubicacion
/*Route::get('/usuario/{id}', [perfilPotatoController::class, 'show']);
Route::put('/usuario/{id}', [perfilPotatoController::class, 'update']);*/

Route::get('/usuario/{id}', [confPerfil::class, 'show']);
Route::put('/usuario/{id}', [confPerfil::class, 'update']);
