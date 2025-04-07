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
            'ubicacion' => 'nullable|string',
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

    public function getProductosByCategorias(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'categorias' => 'required|array',
            'categorias.*' => 'exists:categorias,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Obtener los productos que pertenezcan a las categorías enviadas
        $productos = Producto::whereHas('categorias', function ($query) use ($request) {
            $query->whereIn('categorias.id', $request->categorias);
        })->with('categorias')->get();

        return response()->json($productos);
    }

    public function showProductoDetallado($id)
    {
        $producto = Producto::with(['categorias', 'opiniones.user'])->find($id);

        if (!$producto) {
            return response()->json(['error' => 'Producto no encontrado'], 404);
        }

        return response()->json($producto);
    }
}
