<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Categoria;

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
        $request->validate([
            'nombre' => 'required|string',
            'descripcion' => 'nullable|string',
            'precio' => 'required|integer',
            'estado' => 'required|string',
            'oferta' => 'boolean',
            'user_id' => 'required|exists:users,id',
            'categorias' => 'array', // Debe ser un array de IDs de categorías
            'categorias.*' => 'exists:categorias,id',
        ]);

        $producto = Producto::create($request->except('categorias'));

        // Si se envían categorías, asociarlas
        if ($request->has('categorias')) {
            $producto->categorias()->attach($request->categorias);
        }

        return response()->json(['message' => 'Producto creado correctamente', 'producto' => $producto], 201);
    }

    // Obtener todas las categorías con sus productos
    public function indexCategorias()
    {
        $categorias = Categoria::with('productos')->get();

        return response()->json($categorias);
    }

    // Crear una categoría
    public function storeCategoria(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|unique:categorias,nombre',
        ]);

        $categoria = Categoria::create($request->all());

        return response()->json(['message' => 'Categoría creada correctamente', 'categoria' => $categoria], 201);
    }
}
