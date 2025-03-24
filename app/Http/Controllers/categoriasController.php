<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categoria;

class categoriasController extends Controller
{
    // Obtener todas las categorías con sus productos
    public function indexCategorias()
    {
        $categorias = Categoria::all();
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
