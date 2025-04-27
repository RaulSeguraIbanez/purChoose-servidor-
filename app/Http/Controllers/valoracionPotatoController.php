<?php

namespace App\Http\Controllers;

use App\Models\valoracionPotato;
use Illuminate\Http\Request;

class valoracionPotatoController extends Controller
{
    // Mostrar todas las valoraciones de un producto
    public function index($producto_id)
    {
        $valoraciones = valoracionPotato::where('producto_id', $producto_id)->get();
        return response()->json($valoraciones);
    }

    // Registrar una nueva valoración
    public function store(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'usuario_id' => 'required|exists:users,id',
            'puntuacion' => 'required|integer|min:0|max:5',
        ]);

        $valoracion = valoracionPotato::create([
            'producto_id' => $request->producto_id,
            'usuario_id' => $request->usuario_id,
            'puntuacion' => $request->puntuacion,
        ]);

        return response()->json([
            'message' => 'Valoración creada correctamente',
            'valoracion' => $valoracion
        ], 201);
    }
}
