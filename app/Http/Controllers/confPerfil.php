<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Exception;

class confPerfil extends Controller
{
    /**
     * Muestra los detalles de un usuario específico.
     *
     * @param int $id ID del usuario.
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            // Buscar al usuario por su ID
            $usuario = User::find($id);

            // Si el usuario no existe, devolver un error 404
            if (!$usuario) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no encontrado'
                ], 404);
            }

            // Devolver los datos del usuario
            return response()->json([
                'success' => true,
                'user' => [
                    'name' => $usuario->name,
                    'email' => $usuario->email,
                    'role' => $usuario->role,
                    'fotoPerfil' => $usuario->fotoPerfil,
                    'telefono' => $usuario->telefono,
                    'ubicacion' => $usuario->ubicacion,
                ]
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error inesperado al obtener los datos del usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualiza los datos de un usuario específico.
     *
     * @param Request $request Datos de la solicitud.
     * @param int $id ID del usuario.
     * @return \Illuminate\Http\JsonResponse
     */
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
                'name'    => 'required|string|max:50',
                'email'     => 'required|string|email|max:50|unique:users,email,' . $id,
                'prefijo'   => 'required|string',
                'telefono'  => 'nullable|string|max:20',
                'password'  => 'nullable|string|min:6|max:25|confirmed',
                'password_confirmation' => 'nullable|string|min:6|max:25'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errores en la validación de datos',
                    'errors'  => $validator->errors()
                ], 422);
            }

            // Concatenar teléfono y prefijo
            $telefonoCompleto = $request->telefono ? $request->prefijo . $request->telefono : null;

            // Actualizar usuario
            $usuario->update([
                'name' => $request->name,
                'email' => $request->email,
                'telefono' => $telefonoCompleto,
                'ubicacion' => $request->ubicacion,
                'password' => $request->password ? Hash::make($request->password) : $usuario->password,
            ]);

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