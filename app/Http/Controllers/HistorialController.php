<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Historial;
use Illuminate\Support\Facades\Validator;

class HistorialController extends Controller
{
    /**
     * Mostrar el historial de un usuario específico.
     *
     * @param int $user_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($user_id)
    {
        // Obtener el historial del usuario con los datos del producto relacionado
        $historial = Historial::where('user_id', $user_id)
                              ->with('producto') // Asegúrate de que el modelo Historial tenga esta relación
                              ->get();

        return response()->json([
            'success' => true,
            'message' => 'Historial obtenido exitosamente',
            'data'    => $historial,
        ], 200);
    }

    /**
     * Eliminar un registro del historial.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            // Buscar el registro en el historial
            $registro = Historial::find($id);

            if (!$registro) {
                return response()->json([
                    'success' => false,
                    'message' => 'Registro no encontrado',
                ], 404);
            }

            // Eliminar el registro
            $registro->delete();

            return response()->json([
                'success' => true,
                'message' => 'Registro eliminado exitosamente',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al eliminar el registro',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Actualizar un registro del historial.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            // Buscar el registro en el historial
            $registro = Historial::find($id);

            if (!$registro) {
                return response()->json([
                    'success' => false,
                    'message' => 'Registro no encontrado',
                ], 404);
            }

            // Validar los datos enviados
            $validator = Validator::make($request->all(), [
                'cantidad' => 'nullable|integer|min:1',
                'estado'   => 'nullable|in:pagado,recibido,enviado,cancelado',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errores de validación',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            // Actualizar los campos permitidos
            if ($request->has('cantidad')) {
                $registro->cantidad = $request->cantidad;
            }

            if ($request->has('estado')) {
                $registro->estado = $request->estado;
            }

            $registro->save();

            return response()->json([
                'success' => true,
                'message' => 'Registro actualizado exitosamente',
                'data'    => $registro,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al actualizar el registro',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}