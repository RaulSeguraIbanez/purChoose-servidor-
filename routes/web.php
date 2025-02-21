<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('auth.register');
});

// Página de registro (puedes tener una vista para ello)
Route::get('/register', function () {
    return view('auth.register');
})->name('register');

// Ruta para procesar el registro
Route::post('/register', [AuthController::class, 'register']);

// Página de login (vista)
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// Ruta para procesar el login
Route::post('/login', [AuthController::class, 'login']);

// Ruta para cerrar sesión (usualmente protegida)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Ruta protegida de ejemplo
Route::middleware('auth')->get('/dashboard', function () {
    return view('dashboard');
});
