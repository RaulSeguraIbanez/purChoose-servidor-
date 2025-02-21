<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller

{
    /**
     * Registro de usuario.
     */
    public function register(Request $request)
    {
        $request->validate([
            'nombre'    => 'required|string|max:50',
            'email'     => 'required|string|email|max:50|unique:usuarios',
            'prefijo'   => 'required|string',
            'telefono'  => 'nullable|string|max:20',
            'password'  => 'required|string|min:6|max:25|confirmed',
        ]);

        // Concatenamos el prefijo con el número de teléfono
        $telefonoCompleto = $request->telefono ? $request->prefijo . $request->telefono : null;

        // Crear el usuario
        $user = User::create([
            'nombre'       => $request->nombre,
            'email'        => $request->email,
            'telefono'     => $telefonoCompleto,
            'password'     => Hash::make($request->password),
            'role'         => 'usuario',
            'fechaRegistro' => now(),
            'fotoPerfil'   => 'storage/images/userProfPic/user_profilepic_default.jpg',
        ]);

        Auth::login($user);

        return redirect()->intended('/dashboard')->with('success', 'Usuario registrado exitosamente');
    }
    /**
     * Inicio de sesión.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Credenciales incorrectas'], 401);
        }

        // Generar token de acceso personal
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Inicio de sesión exitoso',
            'token' => $token,
            'user' => $user
        ]);
    }

    /**
     * Cierre de sesión.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Sesión cerrada correctamente.');
    }
}
