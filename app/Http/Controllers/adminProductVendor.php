<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\ImagePr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class adminProductVendor extends Controller
{
    // Obtener productos del usuario con sus categorías e imágenes
    public function getProductsByUser($userId)
    {
        $productos = Producto::with('categorias')
            ->where('user_id', $userId)
            ->get();

        return response()->json([
            'message' => 'Productos del usuario obtenidos correctamente',
            'productos' => $productos,
        ], 200);
    }

    // Actualizar un producto (sin imágenes)
    public function updateProduct(Request $request, $id)
    {
        $producto = Producto::find($id);
        if (!$producto) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'string|max:255',
            'descripcion' => 'nullable|string',
            'precio' => 'numeric',
            'estado' => 'nullable|string',
            'ubicacion' => 'nullable|string',
            'oferta' => 'boolean',
            'individual' => 'boolean',
            'categoria_ids' => 'array',
            'categoria_ids.*' => 'integer|exists:categorias,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Actualizar producto
        $producto->update($request->only([
            'nombre',
            'descripcion',
            'precio',
            'estado',
            'ubicacion',
            'oferta',
            'individual'
        ]));

        // Sincronizar categorías si se envían
        if ($request->has('categoria_ids')) {
            $producto->categorias()->sync($request->categoria_ids);
        }

        return response()->json([
            'message' => 'Producto actualizado correctamente',
            'producto' => $producto->load('categorias'), // Cargar las categorías del producto
        ], 200);
    }
}
