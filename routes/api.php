<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\confPerfil;
use App\Http\Controllers\productosController;
use App\Http\Controllers\perfilPotatoController;


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
Route::post('/login', [AuthController::class, 'login']);

/*Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/perfil', function (Request $request) {
        return response()->json($request->user());
    });
}); */

Route::get('/productos', [productosController::class, 'indexProductos']);
Route::post('/productos', [productosController::class, 'storeProducto']);

Route::get('/categorias', [productosController::class, 'indexCategorias']);
Route::post('/categorias', [productosController::class, 'storeCategoria']);

Route::apiResource('perfil', confPerfil::class);

Route::get('/usuario/{id}', [perfilPotatoController::class, 'show']);
Route::put('/usuario/{id}', [perfilPotatoController::class, 'update']);
