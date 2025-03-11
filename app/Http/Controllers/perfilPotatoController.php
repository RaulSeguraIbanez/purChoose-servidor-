<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Exception;

class perfilPotatoController extends Controller
{

    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'user' => $user
        ], 200);
    }

    public function update(Request $request, $id)
    {
        try {
            $usuario = User::find($id);

            if (!$usuario) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no encontrado'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'password' => 'nullable|string|min:6|max:25|confirmed',
                'password_confirmation' => 'nullable|string|min:6|max:25',
                'ubicacion' => 'nullable|string|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errores en la validación de datos',
                    'errors'  => $validator->errors()
                ], 422);
            }

            // Actualizar solo los campos permitidos
            if ($request->filled('password')) {
                $usuario->password = Hash::make($request->password);
            }

            if ($request->filled('ubicacion')) {
                $usuario->ubicacion = $request->ubicacion;
            }

            $usuario->save(); // Guardar los cambios

            return response()->json([
                'success' => true,
                'message' => 'Usuario actualizado exitosamente',
                'user' => $usuario
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error inesperado al actualizar el usuario',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
