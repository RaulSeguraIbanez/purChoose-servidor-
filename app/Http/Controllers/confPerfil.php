<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Exception;

class confPerfil extends Controller
{
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
