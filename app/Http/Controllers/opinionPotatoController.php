<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\opinionPotato;

class opinionPotatoController extends Controller
{

    // Mostrar todas las opiniones de un producto
    public function index($product_id)
    {
        return opinionPotato::where('product_id', $product_id)->with('user')->get();
    }

    // Guardar una nueva opinión
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:productos,id',
            'user_id' => 'required|exists:users,id',
            'opinion' => 'required|string|max:1000',
        ]);

        $opinion = opinionPotato::create($request->all());

        return response()->json([
            'message' => 'Opinión guardada correctamente',
            'opinion' => $opinion
        ], 201);
    }
}
