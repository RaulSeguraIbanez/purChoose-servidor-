<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Support\Facades\Validator;

class productosController extends Controller
{
    // Obtener todos los productos con sus categorías asociadas
    public function indexProductos()
    {
        $productos = Producto::with('categorias')->get();

        return response()->json($productos);
    }

    // Crear un producto con categorías asociadas
    public function storeProducto(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string',
            'descripcion' => 'nullable|string',
            'precio' => 'required|float',
            'estado' => 'required|string',
            'oferta' => 'boolean',
            'user_id' => 'required|exists:users,id',
            'categorias' => 'array', // Debe ser un array de IDs de categorías
            'categorias.*' => 'exists:categorias,id',
        ]);

        // Crear el producto sin las categorías
        $producto = Producto::create($request->except('categorias'));

        // Asociar el producto con las categorías (tabla intermedia cate_prod)
        if ($request->has('categorias')) {
            $producto->categorias()->sync($request->categorias);
        }

        return response()->json(['message' => 'Producto creado correctamente', 'producto' => $producto], 201);
    }

}
