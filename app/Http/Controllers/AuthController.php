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
        // Validamos las credenciales.
        $request->validate([
            'email'    => 'required|string|email|max:50',
            'password' => 'required|string|max:25',
        ]);

        $credentials = $request->only('email', 'password');

        // Intentamos autenticar al usuario.
        if (Auth::attempt($credentials)) {
            // Regeneramos la sesión para evitar fijación de sesión.
            $request->session()->regenerate();
            return redirect()->intended('/dashboard')->with('success', 'Inicio de sesión exitoso');
        }

        // Si las credenciales no coinciden, retornamos con error.
        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no son correctas.',
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
