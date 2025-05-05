<?php

namespace App\Http\Controllers;

use App\Models\Carrito;
use App\Models\Producto;
use Illuminate\Http\Request;
use App\Models\Historial;

class carritoController extends Controller
{
   // 1. Listar los items del carrito de un usuario
    //    GET /api/carrito/{user_id}
    public function index($user_id)
    {
        // Trae todos los registros de la tabla 'carrito'
        // donde user_id coincide, e incluye datos del producto
        $items = Carrito::where('user_id', $user_id)
                        ->with('producto')
                        ->get();

        return response()->json($items);
    }

    // 2. Agregar producto al carrito
    //    POST /api/carrito
    //    Body JSON: { "user_id": 1, "producto_id": 5, "cantidad": 2 }
    public function store(Request $request)
    {
        $request->validate([
            'user_id'     => 'required|exists:users,id',
            'producto_id' => 'required|exists:productos,id',
            'cantidad'    => 'required|integer|min:1',
        ]);

        // Creamos el registro en la tabla 'carrito'
        $carrito = Carrito::create([
            'user_id'     => $request->user_id,
            'producto_id' => $request->producto_id,
            'cantidad'    => $request->cantidad,
            'estado'      => 'no pagado', // o lo que quieras por defecto
        ]);

        return response()->json([
            'message' => 'Producto agregado al carrito',
            'data'    => $carrito
        ], 201);
    }

    // 3. Actualizar la cantidad o estado de un item del carrito
    //    PUT /api/carrito/{id}
    //    Body JSON: { "cantidad": 3, "estado": "pagado" }
    public function update(Request $request, $id)
    {
        try {
            $carrito = Carrito::findOrFail($id);
    
            // Validar sólo lo que te interesa actualizar
            $request->validate([
                'cantidad' => 'integer|min:1|nullable',
                'estado'   => 'in:pagado,no pagado,recibido,enviado,cancelado|nullable'
            ]);
    
            if ($request->has('cantidad')) {
                \Log::info('Actualizando cantidad:', ['id' => $id, 'cantidad' => $request->cantidad]);
                $carrito->cantidad = $request->cantidad;
            }
    
            if ($request->has('estado')) {
                \Log::info('Actualizando estado:', ['id' => $id, 'estado' => $request->estado]);
    
                // Si el estado cambia a "pagado", mover el producto al historial
                if ($request->estado === 'pagado') {
                    // Crear un registro en la tabla de historial
                    Historial::create([
                        'user_id' => $carrito->user_id,
                        'producto_id' => $carrito->producto_id,
                        'cantidad' => $carrito->cantidad,
                        'precio_total' => $carrito->producto->precio * $carrito->cantidad, // Asegúrate de que el modelo Producto tenga un campo 'precio'
                        'estado' => 'pagado',
                    ]);
    
                    // Eliminar el producto del carrito
                    $carrito->delete();
    
                    return response()->json([
                        'message' => 'Producto pagado y movido al historial',
                    ]);
                }
    
                // Si no es "pagado", simplemente actualiza el estado
                $carrito->estado = $request->estado;
            }
    
            $carrito->save();
    
            return response()->json([
                'message' => 'Carrito actualizado',
                'data'    => $carrito
            ]);
        } catch (\Exception $e) {
            \Log::error('Error al actualizar el carrito:', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Ocurrió un error inesperado',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    // 4. Eliminar un producto del carrito
    //    DELETE /api/carrito/{id}
    public function destroy($id)
    {
        $carrito = Carrito::findOrFail($id);
        $carrito->delete();

        return response()->json([
            'message' => 'Producto eliminado del carrito'
        ]);
    }

}
