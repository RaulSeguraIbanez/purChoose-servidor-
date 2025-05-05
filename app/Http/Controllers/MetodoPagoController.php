<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MetodoPago;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class MetodoPagoController extends Controller
{
    public function store(Request $request)
    {
        // Mensajes personalizados para la validación
        $messages = [
            'email.required_if' => 'El correo electrónico es obligatorio para los métodos de pago PayPal, Apple Pay y Google Pay.',
            'password.required_if' => 'La contraseña es obligatoria para los métodos de pago PayPal, Apple Pay y Google Pay.',
            'nombre.required_if' => 'El nombre es obligatorio para el método de pago Tarjeta.',
            'num_tarjeta.required_if' => 'El número de tarjeta es obligatorio para el método de pago Tarjeta.',
            'fecha_caducidad.required_if' => 'La fecha de caducidad es obligatoria para el método de pago Tarjeta.',
            'codigo_validacion.required_if' => 'El código de validación es obligatorio para el método de pago Tarjeta.',
        ];

        // Validar los datos recibidos
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'tipo' => 'required|string|in:tarjeta,paypal,apple_pay,google_pay',
            'nombre' => 'nullable|required_if:tipo,tarjeta|string',
            'num_tarjeta' => 'nullable|required_if:tipo,tarjeta|string',
            'fecha_caducidad' => 'nullable|required_if:tipo,tarjeta|string',
            'codigo_validacion' => 'nullable|required_if:tipo,tarjeta|string',
            'email' => 'nullable|required_if:tipo,paypal,apple_pay,google_pay|string|email',
            'password' => 'nullable|required_if:tipo,paypal,apple_pay,google_pay|string|min:6',
        ], $messages);

        // Si la validación falla, devolver errores detallados
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores en la validación de datos',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Crear una nueva instancia del modelo MetodoPago
        $metodoPago = new MetodoPago();
        $metodoPago->user_id = $request->input('user_id');
        $metodoPago->tipo = $request->input('tipo');

        // Procesar según el tipo de método de pago
        if ($request->input('tipo') === 'tarjeta') {
            $metodoPago->nombre = $request->input('nombre');
            $metodoPago->num_tarjeta = Crypt::encryptString($request->input('num_tarjeta'));
            $metodoPago->fecha_caducidad = $request->input('fecha_caducidad');
            $metodoPago->codigo_validacion = Crypt::encryptString($request->input('codigo_validacion'));
        } elseif (in_array($request->input('tipo'), ['paypal', 'apple_pay', 'google_pay'])) {
            $metodoPago->email = $request->input('email');
            $metodoPago->password = Crypt::encryptString($request->input('password'));
        }

        // Guardar el método de pago en la base de datos
        $metodoPago->save();

        // Devolver una respuesta exitosa
        return response()->json(['message' => 'Método de pago guardado correctamente'], 201);
    }

    public function update(Request $request, $id)
    {
        // Buscar el método de pago por su ID
        $metodoPago = MetodoPago::find($id);
    
        if (!$metodoPago) {
            return response()->json([
                'success' => false,
                'message' => 'Método de pago no encontrado'
            ], 404);
        }
    
        // Validar los datos recibidos
        $validated = $request->validate([
            'tipo' => 'required|string|in:tarjeta,paypal,apple_pay,google_pay',
            'nombre' => 'nullable|required_if:tipo,tarjeta|string',
            'num_tarjeta' => 'nullable|required_if:tipo,tarjeta|string',
            'fecha_caducidad' => ['nullable', 'required_if:tipo,tarjeta'],
            'codigo_validacion' => 'nullable|required_if:tipo,tarjeta|string',
            'email' => 'nullable|required_if:tipo,paypal,apple_pay,google_pay|string|email',
            'password' => 'nullable|required_if:tipo,paypal,apple_pay,google_pay|string|min:6',
        ]);
    
        // Actualizar los campos del método de pago
        $metodoPago->tipo = $validated['tipo'];
    
        if ($validated['tipo'] === 'tarjeta') {
            $metodoPago->nombre = $validated['nombre'];
            $metodoPago->num_tarjeta = Crypt::encryptString($validated['num_tarjeta']);
            $metodoPago->fecha_caducidad = $validated['fecha_caducidad'];
            $metodoPago->codigo_validacion = Crypt::encryptString($validated['codigo_validacion']);
        } elseif (in_array($validated['tipo'], ['paypal', 'apple_pay', 'google_pay'])) {
            $metodoPago->email = $validated['email'];
            $metodoPago->password = Crypt::encryptString($validated['password']);
        }
    
        // Guardar los cambios en la base de datos
        $metodoPago->save();
    
        // Devolver una respuesta exitosa
        return response()->json([
            'success' => true,
            'message' => 'Método de pago actualizado correctamente',
            'data' => $metodoPago
        ], 200);
    }
    /**
     * Obtiene todos los métodos de pago del usuario.
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer', // El frontend debe enviar el user_id
        ]);

        $metodosPago = MetodoPago::where('user_id', $validated['user_id'])->get();

        foreach ($metodosPago as $metodo) {
            if ($metodo->tipo === 'tarjeta') {
                $metodo->num_tarjeta = Crypt::decryptString($metodo->num_tarjeta);
                $metodo->codigo_validacion = Crypt::decryptString($metodo->codigo_validacion);
            } elseif ($metodo->tipo === 'paypal') {
                $metodo->password = Crypt::decryptString($metodo->password);
            }
        }

        return response()->json(['data' => $metodosPago], 200);
    }

    /**
     * Elimina un método de pago.
     */
    public function destroy($id_metodo)
    {
        $metodoPago = MetodoPago::findOrFail($id_metodo);
        $metodoPago->delete();

        return response()->json(['message' => 'Método de pago eliminado correctamente'], 200);
    }
}