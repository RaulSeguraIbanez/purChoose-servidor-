<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\confPerfil;
use App\Http\Controllers\productosController;
use App\Http\Controllers\perfilPotatoController;
use App\Http\Controllers\categoriasController;
use App\Http\Controllers\opinionPotatoController;
use App\Http\Controllers\valoracionPotatoController;
use App\Http\Controllers\carritoController;
use App\Http\Controllers\HistorialController;
use App\Http\Controllers\MetodoPagoController;
use App\Http\Controllers\adminProductVendor;

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
Route::post('/registersegaroamigo', [AuthController::class, 'registerVendedor']);
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
Route::get('/productos/{id}/imagenes', [productosController::class, 'getImagesByProducto']);
Route::get('/productos/{id}/with-images', [productosController::class, 'getProductoWithImages']);


Route::get('/productos/with-categories-and-images', [productosController::class, 'getProductsWithCategoriesAndImages']);
Route::get('/productos/with-categories-and-images-caroussel', [productosController::class, 'getProductsWithCategoriesAndImages_Caroussel']);


Route::get('/categorias', [categoriasController::class, 'indexCategorias']);
Route::post('/categorias', [categoriasController::class, 'storeCategoria']);
Route::post('/categorias/productos', [productosController::class, 'getProductosByCategorias']);

Route::apiResource('perfil', confPerfil::class);
Route::get('/productos/{id}/imagenes', [productosController::class, 'getImagesByProducto']);
Route::get('/opiniones/{product_id}', [opinionPotatoController::class, 'index']);
Route::post('/opiniones', [opinionPotatoController::class, 'store']);

// Listar items del carrito de un usuario
/* http://127.0.0.1:8000/api/carrito/3 */ // id del user
Route::get('/carrito/{user_id}', [CarritoController::class, 'index']);
/* http://127.0.0.1:8000/api/carrito  */
Route::post('/carrito', [carritoController::class, 'store']);
/* http://127.0.0.1:8000/api/carrito/item/1 */   //item el id del carrito
Route::put('/carrito/item/{id}', [carritoController::class, 'updateCantidad']);
/* http://127.0.0.1:8000/api/carrito/user/3 */
Route::put('/carrito/user/{user_id}', [carritoController::class, 'updateEstado']);
Route::delete('/carrito/{id}', [carritoController::class, 'destroy']);

//Rutas para modificar datos del usuario y mostrarlos. Solo permite modificar contraseña y ubicacion
/*Route::get('/usuario/{id}', [perfilPotatoController::class, 'show']);
Route::put('/usuario/{id}', [perfilPotatoController::class, 'update']);*/

Route::get('/usuario/{id}', [confPerfil::class, 'show']);
Route::put('/usuario/{id}', [confPerfil::class, 'update']);


// Producto del user
// los productos k ha creado el usuario
Route::get('/productos/por-usuario/{id}', [ProductosController::class, 'porUsuario']);
// obtenemos la imagen del producto por id para poder editar el producto
Route::get('/productos/{id}/imagenes', [productosController::class, 'getImagesByProductId']);
// Obtener categorías por ID de producto
Route::get('/categorias/producto/{id}', [categoriasController::class, 'getByProductId']);
// edita el producto menos categorias y imagenes
Route::put('/productos/{id}', [productosController::class, 'updateEdit']);
// "edita" eliminar una imagen del producto
Route::delete('/imagenes/{imageName}', [productosController::class, 'deleteImageByUrl']);
// actualizar la pta categoria del producto
Route::put('/productos/{id}/categorias', [productosController::class, 'updateCategorias']);
// meter una foto nueva en editar producto
Route::post('/productos/{id}/imagenes', [productosController::class, 'storeImages']);
// elimina el producto en /products
Route::delete('/productos/{id}', [productosController::class, 'eliminarProductuser']);

// Listar el historial de un usuario específico
Route::get('/historial/{user_id}', [HistorialController::class, 'index']);

// Eliminar un registro del historial por su ID
Route::delete('/historial/{id}', [HistorialController::class, 'destroy']);

// Actualizar un registro del historial por su ID
Route::put('/historial/{id}', [HistorialController::class, 'update']);


// Rutas para métodos de pago
Route::post('/metodos-pago', [MetodoPagoController::class, 'store']); // Guardar un método de pago
Route::get('/metodos-pago', [MetodoPagoController::class, 'index']); // Obtener métodos de pago del usuario
Route::delete('/metodos-pago/{id_metodo}', [MetodoPagoController::class, 'destroy']); // Eliminar un método de pago
Route::put('/metodos-pago/{id}', [MetodoPagoController::class, 'update']); // Actualizar un método de pago


/* p0tATo Secti0n */

// Rutas de valoraciones
Route::get('/valoraciones/{producto_id}', [valoracionPotatoController::class, 'index']);
Route::post('/valoraciones', [valoracionPotatoController::class, 'store']);
// enseña el producto con su valoracion good
Route::get('/productos/{id}', [productosController::class, 'showProductinhoPotato']);
Route::delete('/productos/{id}', [productosController::class, 'eliminarProductuser']);

//administracion de productos como vendedor
Route::get('/productosVendor/user/{id}', [adminProductVendor::class, 'getProductsByUser']);
Route::put('/productosVendor/{id}', [adminProductVendor::class, 'updateProduct']);
