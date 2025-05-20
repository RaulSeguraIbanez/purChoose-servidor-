<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Categoria;
use App\Models\Producto;
use App\Models\ImagePr;

class AdminController extends Controller
{
    // === USUARIOS ===

    public function indexUsuarios()
    {
        return response()->json(User::all());
    }

    public function showUsuario($id)
    {
        $usuario = User::find($id);
        if (!$usuario) return response()->json(['error' => 'Usuario no encontrado'], 404);
        return response()->json($usuario);
    }

    public function storeUsuario(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'nullable|in:admin,usuario,empresaurio',
            'ubicacion' => 'nullable|string',
            'telefono' => 'nullable|string',
        ]);

        $usuario = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role ?? 'usuario',
            'ubicacion' => $request->ubicacion,
            'telefono' => $request->telefono,
        ]);

        return response()->json($usuario, 201);
    }

   public function updateUsuario(Request $request, $id)
{
    // Buscar al usuario por ID
    $usuario = User::find($id);
    if (!$usuario) {
        return response()->json(['error' => 'Usuario no encontrado'], 404);
    }

    // Validaciones condicionales
    $validatedData = $request->validate([
        'name' => 'sometimes|required|string|max:255',
        'email' => [
            'sometimes',
            'required',
            'email',
            Rule::unique('users')->ignore($id),
        ],
        'password' => 'sometimes|min:6',
        'role' => ['sometimes', Rule::in(['usuario', 'empresaurio', 'admin'])],
        'ubicacion' => 'sometimes|nullable|string',
        'telefono' => 'sometimes|nullable|string',
    ]);

    // Si hay contraseña, hashearla antes de actualizar
    if ($request->has('password')) {
        $validatedData['password'] = bcrypt($request->input('password'));
    }

    // Actualizar datos del usuario
    $usuario->update($validatedData);

    // Devolver respuesta exitosa con usuario actualizado
    return response()->json($usuario);
}

    public function deleteUsuario($id)
    {
        $usuario = User::find($id);
        if (!$usuario) return response()->json(['error' => 'Usuario no encontrado'], 404);
        $usuario->delete();
        return response()->json(['message' => 'Usuario eliminado correctamente']);
    }

    // === CATEGORÍAS ===

    public function indexCategorias()
    {
        return response()->json(Categoria::with('productos')->get());
    }

    public function showCategoria($id)
    {
        $categoria = Categoria::with('productos')->find($id);
        if (!$categoria) return response()->json(['error' => 'Categoría no encontrada'], 404);
        return response()->json($categoria);
    }

public function storeCategoria(Request $request)
{
    $validated = $request->validate([
        'nombre' => 'required|string|max:255',
    ]);

    // Si imagen no viene, asignamos la URL por defecto
    $validated['imagen'] = $request->input('imagen', 'http://localhost:8000/storage/Images/categoryImages/false.png');

    $categoria = Categoria::create($validated);

    return response()->json($categoria, 201);
}

    // Actualizar una categoría
   public function updateCategoria(Request $request, $id)
{
    $categoria = Categoria::find($id);

    if (!$categoria) {
        return response()->json(['error' => 'Categoría no encontrada'], 404);
    }

    $validated = $request->validate([
        'nombre' => 'required|string|max:255',
    ]);

    // Mantener la imagen actual si no se proporciona una nueva
    $validated['imagen'] = $request->input('imagen', $categoria->imagen);

    $categoria->update($validated);

    return response()->json($categoria);
}
    // Eliminar una categoría
   public function deleteCategoria($id)
    {
        $categoria = Categoria::find($id);

        if (!$categoria) {
            return response()->json(['error' => 'Categoría no encontrada'], 404);
        }

        $categoria->delete();

        return response()->json(['message' => 'Categoría eliminada correctamente']);
    }


    // === PRODUCTOS ===

    public function indexProductos()
    {
        return response()->json(Producto::with(['categorias', 'imagenes', 'usuario'])->get());
    }

    public function showProducto($id)
    {
        $producto = Producto::with(['categorias', 'imagenes', 'usuario'])->find($id);
        if (!$producto) return response()->json(['error' => 'Producto no encontrado'], 404);
        return response()->json($producto);
    }

    public function storeProducto(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'precio' => 'required|numeric',
            'estado' => 'required|string',
            'ubicacion' => 'nullable|string',
            'oferta' => 'boolean',
            'quantity' => 'integer|min:1',
            'individual' => 'boolean',
            'activo' => 'boolean',
            'user_id' => 'required|exists:users,id',
        ]);

        $producto = Producto::create($request->all());

        // Asignar categorías si vienen en la petición
        if ($request->has('categorias')) {
            $producto->categorias()->attach($request->categorias);
        }

        return response()->json($producto->load(['categorias', 'imagenes']), 201);
    }

    public function updateProducto(Request $request, $id)
{
    // Buscar producto con relaciones
    $producto = Producto::with(['categorias', 'imagenes'])->find($id);

    if (!$producto) {
        return response()->json(['error' => 'Producto no encontrado'], 404);
    }

    // Validación solo de campos relevantes
    $validated = $request->validate([
        'nombre' => 'sometimes|required|string|max:255',
        'descripcion' => 'sometimes|required|string',
        'precio' => 'sometimes|required|numeric',
        'estado' => 'sometimes|required|string',
        'ubicacion' => 'sometimes|nullable|string',
        'oferta' => 'sometimes|boolean',
        'user_id' => 'sometimes|required|exists:users,id'
    ]);

    // Limpiar datos antes de actualizar
    $data = collect($validated)->only([
        'nombre', 'descripcion', 'precio', 'estado', 'ubicacion', 'oferta', 'user_id'
    ])->toArray();

    // Actualizar producto con datos limpios
    $producto->update($data);

    // Actualizar categorías si vienen en la solicitud
    if ($request->has('categorias')) {
        $categoriaIds = is_array($request->categorias)
            ? array_map(fn($cat) => is_array($cat) ? $cat['id'] : $cat, $request->categorias)
            : [];

        $producto->categorias()->sync($categoriaIds);
    }

    // Devolver producto actualizado con sus relaciones
    return response()->json($producto->fresh(['categorias', 'imagenes']));
}

    public function deleteProducto($id)
    {
        $producto = Producto::find($id);
        if (!$producto) return response()->json(['error' => 'Producto no encontrado'], 404);
        $producto->delete();
        return response()->json(['message' => 'Producto eliminado correctamente']);
    }
}