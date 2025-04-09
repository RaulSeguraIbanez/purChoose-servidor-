<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\ImagePr; // Cambia "ImagenPr" por "ImagePr"
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class productosController extends Controller
{
    // Obtener todos los productos con sus categorías asociadas
    public function indexProductos()
    {
        $productos = Producto::with('categorias')->get();

        return response()->json($productos);
    }

    // Crear un producto con categorías asociadas y subir imágenes
    public function storeProducto(Request $request)
    {
        // Validar los datos del producto y las imágenes
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0', // Cambia 'float' por 'numeric'
            'estado' => 'required|string|in:nuevo,usado',
            'oferta' => 'boolean',
            'ubicacion' => 'nullable|string',
            'user_id' => 'required|integer|exists:users,id',
            'categorias' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Crear el producto sin las categorías ni imágenes
        $producto = Producto::create($request->except(['categorias', 'images']));

        // Asociar el producto con las categorías (tabla intermedia cate_prod)
        if ($request->has('categorias')) {
            $producto->categorias()->sync($request->categorias);
        }

        // Subir imágenes y asociarlas con el producto
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                // Generar un nombre único para la imagen
                $imageName = time() . '_' . $image->getClientOriginalName();

                // Guardar la imagen en la carpeta storage/app/public/images/productImages/
                $path = $image->storeAs('public/Images/productImages', $imageName);

                // Crear un registro en la tabla imagenes_pr
                $imagenPr = new ImagenPr();
                $imagenPr->url = Storage::url($path); // URL pública de la imagen
                $imagenPr->producto_id = $producto->id;
                $imagenPr->save();
            }
        }

        return response()->json([
            'message' => 'Producto creado correctamente',
            'producto' => $producto,
        ], 201);
    }
    public function storeImages(Request $request, $productoId)
    {
        $validator = Validator::make($request->all(), [
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        foreach ($request->file('images') as $image) {
            $imageName = time() . '_' . $image->getClientOriginalName();
            $path = $image->storeAs('public/images/productImages', $imageName);

            $imagenPr = new ImagePr(); // Cambia "ImagenPr" por "ImagePr"
            $imagenPr->url = Storage::url($path);
            $imagenPr->producto_id = $productoId;
            $imagenPr->save();
        }

        return response()->json(['message' => 'Imágenes subidas correctamente'], 201);
    }
    // Obtener productos filtrados por categorías
    public function getProductosByCategorias(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'categorias' => 'required|array',
            'categorias.*' => 'exists:categorias,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Obtener los productos que pertenezcan a las categorías enviadas
        $productos = Producto::whereHas('categorias', function ($query) use ($request) {
            $query->whereIn('categorias.id', $request->categorias);
        })->with('categorias')->get();

        return response()->json($productos);
    }

    // Mostrar detalles de un producto específico
    public function showProductoDetallado($id)
    {
        $producto = Producto::with(['categorias', 'opiniones.user'])->find($id);

        if (!$producto) {
            return response()->json(['error' => 'Producto no encontrado'], 404);
        }

        return response()->json($producto);
    }
}