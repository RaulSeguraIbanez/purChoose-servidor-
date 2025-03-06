<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Validación de los datos del formulario
        $validator = Validator::make($request->all(), [
            'name'    => 'required|string|max:50',
            'email'     => 'required|string|email|max:50|unique:users,email', // Asegúrate de usar la tabla correcta
            'prefijo'   => 'required|string',
            'telefono'  => 'nullable|string|max:20',
            'password'  => 'required|string|min:6|max:25|confirmed',
        ]);

        // Concatenamos el prefijo con el número de teléfono
        $telefonoCompleto = $request->telefono ? $request->prefijo . $request->telefono : null;

        // Crear el usuario

        $user = User::create([
            'name'       => $request->name,
            'email'        => $request->email,
            'telefono'     => $telefonoCompleto,
            'password'     => Hash::make($request->password),
            'role'         => 'usuario',
            'fechaRegistro' => now(),
            'fotoPerfil'   => 'storage/images/userProfPic/user_profilepic_default.jpg',
        ]);

        // Generar token de acceso personal
        $token = $user->createToken('auth_token')->plainTextToken;

        // Devolver respuesta JSON
        return response()->json([
            'message' => 'Usuario registrado exitosamente',
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    /**
     * Inicio de sesión.
     */
    public function login(Request $request)
    {
        // Validación de los datos del formulario
        $request->validate([
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);

        // Buscar al usuario por correo electrónico
        $user = User::where('email', $request->email)->first();

        // Verificar credenciales
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Credenciales incorrectas'], 401);
        }

        // Generar token de acceso personal
        $token = $user->createToken('auth_token')->plainTextToken;

        // Devolver respuesta JSON
        return response()->json([
            'message' => 'Inicio de sesión exitoso',
            'token' => $token,
            'user' => $user,
        ]);
    }

    /**
     * Cierre de sesión.
     */
    public function logout(Request $request)
    {
        // Revocar el token actual
        $request->user()->currentAccessToken()->delete();

        // Devolver respuesta JSON
        return response()->json(['message' => 'Sesión cerrada correctamente']);
    }
}
