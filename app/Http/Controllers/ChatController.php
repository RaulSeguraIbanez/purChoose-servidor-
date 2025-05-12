<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\Mensaje;
use Illuminate\Support\Facades\Auth;
use App\Models\User;


class ChatController extends Controller
{  
    // 1. Crear un nuevo chat manualmente
    public function createChat(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'usuario1_id' => 'required|exists:users,id',
            'usuario2_id' => 'required|exists:users,id',
        ]);

        $existingChat = Chat::where('producto_id', $request->producto_id)
            ->where(function ($query) use ($request) {
                $query->where([
                    ['usuario1_id', $request->usuario1_id],
                    ['usuario2_id', $request->usuario2_id]
                ])->orWhere([
                    ['usuario1_id', $request->usuario2_id],
                    ['usuario2_id', $request->usuario1_id]
                ]);
            })
            ->first();

        if ($existingChat) {
            return response()->json([
                'message' => 'Ya existe un chat entre estos usuarios para este producto.',
                'chat' => $existingChat
            ], 409);
        }

        $chat = Chat::create([
            'producto_id' => $request->producto_id,
            'usuario1_id' => $request->usuario1_id,
            'usuario2_id' => $request->usuario2_id
        ]);

        return response()->json([
            'message' => 'Chat creado correctamente.',
            'chat' => $chat
        ], 201);
    }

    // 2. Enviar un mensaje a un chat
   public function sendMessage(Request $request, $chatId)
{
    // Validar contenido y usuario_id
    $request->validate([
        'contenido' => 'required|string',
        'usuario_id' => 'required|exists:users,id'
    ]);

    $usuarioId = $request->input('usuario_id');

    $mensaje = Mensaje::create([
        'chat_id' => $chatId,
        'usuario_id' => $usuarioId,
        'contenido' => $request->input('contenido'),
        'leido' => false
    ]);

    return response()->json($mensaje->load('usuario'), 201);
}
    // 3. Listar todos los chats del usuario logueado
public function getAllChatsByUser($id_usuario)
{
    // Validar que el usuario exista
    $usuario = User::find($id_usuario);

    if (!$usuario) {
        return response()->json(['message' => 'Usuario no encontrado'], 404);
    }

    // Recuperar todos los chats donde participa este usuario
    $chats = Chat::where('usuario1_id', $id_usuario)
                 ->orWhere('usuario2_id', $id_usuario)
                 ->with(['mensajes' => function ($q) {
                     $q->latest()->take(1);
                 }, 'usuario1', 'usuario2'])
                 ->get();

    return response()->json($chats, 200);
}

    // 4. Eliminar un chat y sus mensajes
    public function deleteChat($id)
    {
        $chat = Chat::with('mensajes')->find($id);

        if (!$chat) {
            return response()->json(['message' => 'Chat no encontrado'], 404);
        }

        // Borrar todos los mensajes del chat
        foreach ($chat->mensajes as $mensaje) {
            $mensaje->delete();
        }

        // Borrar el chat
        $chat->delete();

        return response()->json(['message' => 'Chat eliminado completamente'], 200);
    }

    // 5. Recuperar todos los mensajes de un chat
    public function getAllMensajesByChat($chatId)
    {
        $mensajes = Mensaje::where('chat_id', $chatId)
                           ->with('usuario')
                           ->orderBy('created_at', 'asc')
                           ->get();

        if ($mensajes->isEmpty()) {
            return response()->json(['message' => 'No hay mensajes en este chat'], 404);
        }

        return response()->json($mensajes, 200);
    }

    // 6. Actualizar un mensaje
    public function updateMensaje(Request $request, $chatId, $mensajeId)
{
    $request->validate([
        'contenido' => 'required|string'
    ]);

    $mensaje = Mensaje::where('id', $mensajeId)
                      ->where('chat_id', $chatId)
                      ->first();

    if (!$mensaje) {
        return response()->json(['message' => 'Mensaje no encontrado'], 404);
    }

    $mensaje->update([
        'contenido' => $request->input('contenido')
    ]);

    return response()->json(['message' => 'Mensaje actualizado correctamente', 'mensaje' => $mensaje], 200);
}

    // 7. Eliminar un mensaje
    public function deleteMensaje($chatId, $mensajeId)
    {
        $mensaje = Mensaje::where('id', $mensajeId)
                          ->where('chat_id', $chatId)
                          ->first();

        if (!$mensaje) {
            return response()->json(['message' => 'Mensaje no encontrado en este chat'], 404);
        }

        $mensaje->delete();

        return response()->json(['message' => 'Mensaje eliminado'], 200);
    }
}