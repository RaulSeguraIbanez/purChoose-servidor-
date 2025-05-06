<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage; // Importa Storage para manejar archivos
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
            return response()->json(['success' => false, 'message' => 'Usuario no encontrado'], 404);
        }

        // Validar los datos recibidos
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
            'email' => 'required|string|email|max:50|unique:users,email,' . $id,
            'prefijo' => 'required|string',
            'telefono' => 'nullable|string',
            'ubicacion' => 'required|string',
            'password' => 'nullable|string|min:8|confirmed',
            'fotoPerfil' => 'nullable|string', // Imagen codificada en Base64
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()], 422);
        }

        // Guardar los datos básicos
        $usuario->update([
            'name' => $request->name,
            'email' => $request->email,
            'telefono' => $request->telefono,
            'ubicacion' => $request->ubicacion,
            'password' => $request->password ? Hash::make($request->password) : $usuario->password,
        ]);

        // Manejar la carga de la imagen
        if ($request->fotoPerfil) {
            // Eliminar la imagen anterior si existe
            if ($usuario->fotoPerfil) {
                Storage::delete(str_replace('storage', 'public', $usuario->fotoPerfil));
            }

            // Decodificar la imagen Base64
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->fotoPerfil));

            // Guardar la imagen en el sistema de archivos
            $imageName = 'user_' . $id . '_' . time() . '.png'; // Nombre único para la imagen
            Storage::disk('public')->put('Images/userProfPic/' . $imageName, $imageData);

            // Guardar la URL en la base de datos
            $usuario->update(['fotoPerfil' => '/storage/Images/userProfPic/' . $imageName]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Usuario actualizado exitosamente',
            'user' => $usuario,
        ], 200);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Ocurrió un error inesperado al actualizar el usuario',
            'error' => $e->getMessage(),
        ], 500);
    }
}

}
