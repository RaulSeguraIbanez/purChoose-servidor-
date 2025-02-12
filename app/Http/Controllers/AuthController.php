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
        // Validamos los datos recibidos.
        $request->validate([
            'nombre'         => 'required|string|max:19',
            'apellidos'      => 'required|string|max:30',
            'email'          => 'required|string|email|max:50|unique:usuarios',
            'password'       => 'required|string|min:6|max:25|confirmed',
        ]);

        // Concatenamos "nombre" y "apellidos".
        $nombreCompleto = $request->nombre . ' ' . $request->apellidos;

        // Creamos el usuario.
        $user = User::create([
            'nombre'       => $nombreCompleto,
            'email'        => $request->email,
            'password'     => Hash::make($request->password),
            // Asignamos un rol por defecto (puedes ajustar según tu lógica)
            'role'         => 'usuario',
            // Establecemos la fecha de registro actual
            'fechaRegistro' => now(),
            // Puedes incluir otros campos si están disponibles en el request (ubicacion, telefono, etc.)
        ]);

        // Opcional: iniciar sesión automáticamente tras el registro.
        Auth::login($user);

        // Redirigimos o retornamos una respuesta según convenga.
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