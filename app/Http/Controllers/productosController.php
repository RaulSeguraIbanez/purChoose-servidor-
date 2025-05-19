<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\ImagePr; //"ImagePr"
use App\Models\User; // Import the User model
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

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
                $path = $image->storeAs('public/app/Images/productImages', $imageName);

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

    public function storeProductoEmpresaurio(Request $request)
    {
        // Validar los datos del producto y las imágenes
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'estado' => 'required|string|in:nuevo,usado',
            'oferta' => 'boolean',
            'individual' => 'boolean',
            'quantity' => 'integer|min:2',
            'ubicacion' => 'nullable|string',
            'user_id' => 'required|integer|exists:users,id',
            'categorias' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Sobrescribir manualmente el campo 'individual' a false
        $datos = $request->except(['categorias', 'images']);
        $datos['individual'] = false;

        // Crear el producto con el valor de 'individual' ya forzado
        $producto = Producto::create($datos);

        // Asociar el producto con las categorías
        if ($request->has('categorias')) {
            $producto->categorias()->sync($request->categorias);
        }

        // Subir imágenes
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . $image->getClientOriginalName();
                $path = $image->storeAs('public/app/Images/productImages', $imageName);

                $imagenPr = new ImagenPr();
                $imagenPr->url = Storage::url($path);
                $imagenPr->producto_id = $producto->id;
                $imagenPr->save();
            }
        }

        return response()->json([
            'message' => 'Producto creado correctamente',
            'producto' => $producto,
        ], 201);
    }

    // Obtener todos los productos con sus categorías e imágenes
    public function getProductsWithCategoriesAndImages()
    {
        // Cargar productos con sus relaciones
        $productos = Producto::with(['categorias', 'imagenes'])->get();

        // Formatear las imágenes para incluir URLs absolutas
        $productos = $productos->map(function ($producto) {
            $producto->imagenes = $producto->imagenes->map(function ($imagen) {
                return [
                    'id' => $imagen->id,
                    'url' => asset($imagen->url), // Genera una URL absoluta
                ];
            });
            return $producto;
        });

        return response()->json([
            'message' => 'Productos obtenidos correctamente',
            'productos' => $productos,
        ], 200);
    }

    public function getProductsWithCategoriesAndImages_Caroussel()
    {
        // Obtener 20 productos aleatorios con sus relaciones
        $productos = Producto::with(['categorias', 'imagenes'])
            ->inRandomOrder()
            ->take(20)
            ->get();

        // Formatear las imágenes para incluir URLs absolutas
        $productos = $productos->map(function ($producto) {
            $producto->imagenes = $producto->imagenes->map(function ($imagen) {
                return [
                    'id' => $imagen->id,
                    'url' => asset($imagen->url), // Genera una URL absoluta
                ];
            });
            return $producto;
        });

        return response()->json([
            'message' => 'Productos aleatorios obtenidos correctamente',
            'productos' => $productos,
        ], 200);
    }

public function getProductsWithCategoriesAndImagesByCategory(Request $request)
{
    $categoriaIds = $request->input('categorias');

    // Obtener todos los productos filtrados por categoría (intersección)
    $query = Producto::with(['categorias', 'imagenes']);

    // Si hay categorías, filtrar productos que tengan TODAS esas categorías
    if (!empty($categoriaIds) && is_array($categoriaIds)) {
        foreach ($categoriaIds as $categoriaId) {
            $query->whereHas('categorias', function ($q) use ($categoriaId) {
                $q->where('categorias.id', $categoriaId);
            });
        }
    }

    $productos = $query->get();

    // Formatear imágenes con URL absoluta
    $productos = $productos->map(function ($producto) {
        $producto->imagenes = $producto->imagenes->map(function ($imagen) {
            return [
                'id' => $imagen->id,
                'url' => asset($imagen->url),
            ];
        });

        return $producto;
    });

    return response()->json([
        'message' => 'Productos obtenidos correctamente',
        'productos' => $productos
    ], 200);
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

          /*  $path = $image->storeAs('/storage/app/public/images/productImages', $imageName); */

            $imagenPr = new ImagePr();
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
    // Obtener todas las imágenes asociadas a un producto específico
    public function getImagesByProducto($productoId)
    {
        // Buscar el producto por su ID
        $producto = Producto::find($productoId);

        if (!$producto) {
            return response()->json(['error' => 'Producto no encontrado'], 404);
        }

        // Obtener las imágenes asociadas al producto
        $imagenes = $producto->imagenes; // Usando la relación definida en el modelo Producto

        // Formatear las imágenes para devolver solo las URLs
        $urls = $imagenes->map(function ($imagen) {
            return [
                'id' => $imagen->id,
                'url' => $imagen->url,
            ];
        });

        return response()->json([
            'message' => 'Imágenes obtenidas correctamente',
            'imagenes' => $urls,
        ], 200);
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

    // productos del usuario ardeiii
    // por puto vago andrei pilla el producto del usuario y las visitas en vez de hacer uno nuevo
   public function porUsuario($id, Request $request)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $query = Producto::with('imagenes')
            ->where('user_id', $id);

        // Filtrar según query string
        if ($request->query('inactivos') === 'true') {
            $query->where('activo', false);
        } else {
            $query->where('activo', true);
        }

        $productos = $query->get()->map(function ($producto) {
            return [
                'id' => $producto->id,
                'titulo' => $producto->nombre,
                'precio' => $producto->precio,
                'publicado' => $producto->created_at->format('d/m/Y'),
                'modificado' => $producto->updated_at->format('d/m/Y'),
                'imagen' => $producto->imagenes->first()
                    ? asset('storage/images/productImages/' . basename($producto->imagenes->first()->url))
                    : null,
                'views' => $producto->views,
                'ventas' => $producto->ventas,
                'activo' => $producto->activo,
            ];
        });

        return response()->json([
            'user' => [
                'id' => $user->id,
                'nombre' => $user->name,
                'email' => $user->email,
            ],
            'productos' => $productos
        ]);
    }


    public function eliminarProductuser($id)
    {
        $producto = Producto::find($id);

        if (!$producto) {
            return response()->json(['error' => 'Producto no encontrado'], 404);
        }

        // Obtener todas las imágenes relacionadas
        $imagenes = ImagePr::where('producto_id', $id)->get();

        foreach ($imagenes as $img) {
            // Elimina el archivo del disco si existe
            $path = storage_path('images/productImages/' . basename($img->url));
            if (file_exists($path)) {
                unlink($path);
            }

            // Elimina el registro de la imagen
            $img->delete();
        }

        // Eliminar el producto
        $producto->delete();

        return response()->json(['message' => 'Producto eliminado correctamente'], 200);
    }

    public function editarProductuser(Request $request, $id)
    {
        $producto = Producto::find($id);

        if (!$producto) {
            return response()->json(['error' => 'Producto no encontrado'], 404);
        }

        // Validar los datos del producto
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'estado' => 'required|string|in:nuevo,usado',
            'oferta' => 'boolean',
            'ubicacion' => 'nullable|string',
            'user_id' => 'required|integer|exists:users,id',
            'categorias' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Actualizar el producto
        $producto->update($request->except('categorias'));

        // Actualizar las categorías
        if ($request->has('categorias')) {
            $producto->categorias()->sync($request->categorias);
        }

        return response()->json([
            'message' => 'Producto actualizado correctamente',
            'producto' => $producto,
        ], 200);
    }



    // Obtener imágenes por ID de producto "para el carrousel"
    public function getImagesByProductId($id)
    {
        $producto = Producto::with('imagenes')->find($id);

        if (!$producto) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        return response()->json([
            'imagenes' => $producto->imagenes
        ]);
    }


    // Las foticos del producto a editar
    public function getImagesByProductIdForEdit($id)
    {
        $imagenes = ImagePr::where('producto_id', $id)->get();
        $producto = Producto::with('imagenes')->find($id);

        $imagenes = $imagenes->map(function ($imagen) {
            // Asegúrate de que devuelva la URL completa y correcta
            return url('storage/images/productImages/' . basename($imagen->url));
        });
        if (!$producto) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        return response()->json($imagenes);
        return response()->json([
            'imagenes' => $producto->imagenes
        ]);
    }



    // Obtener un producto específico con sus categorías, imágenes y opiniones
    // potatoProducto
    public function showProductinhoPotato($id)
    {
        $producto = Producto::with([
            'categorias',
            'imagenes',         // Añadimos imágenes también si quieres mostrar todo junto
            'opiPotatoe.user',    // Opiniones con el usuario que opinó
            'valorinhaGood.usuario' // Valoraciones con el usuario que valoró
        ])->find($id);

        if (!$producto) {
            return response()->json(['error' => 'Producto no encontrado'], 404);
        }

        return response()->json($producto);
    }

    public function updateEdit(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);

        $producto->update($request->all());

        return response()->json(['message' => 'Producto actualizado correctamente', 'producto' => $producto]);
    }


    public function deleteImageByUrl($imageName)
    {
        if (!$imageName) {
            return response()->json(['error' => 'No se proporcionó el nombre de la imagen'], 400);
        }

        $image = ImagePr::where('url', 'like', "%$imageName")->first();

        if (!$image) {
            return response()->json(['error' => 'Imagen no encontrada'], 404);
        }

        $filePath = public_path("storage/images/productImages/$imageName");
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $image->delete();

        return response()->json(['success' => true]);
    }
    // Eliminar todas las imágenes de un producto específico
    public function deleteAllByProductId($id)
    {
        $imagenes = ImagePr::where('producto_id', $id)->get();

        foreach ($imagenes as $imagen) {
            $path = str_replace('/storage/', 'public/', $imagen->url);
            Storage::delete($path);

            $imagen->delete();
        }

        return response()->json(['message' => 'Todas las imágenes fueron eliminadas.']);
    }



    public function updateCategorias(Request $request, $id)
    {
        $producto = Producto::find($id);

        if (!$producto) {
            return response()->json(['error' => 'Producto no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'categorias' => 'required|array',
            'categorias.*' => 'exists:categorias,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $producto->categorias()->sync($request->categorias);

        return response()->json(['message' => 'Categorías actualizadas correctamente'], 200);
    }

    //visitas
    public function incrementViews($id)
    {
        Log::info("Vista registrada para producto ID: $id");

        $producto = Producto::find($id);
        if ($producto) {
            $producto->views += 1;
            $producto->save();
            return response()->json(['message' => 'Visita registrada'], 200);
        }

        return response()->json(['message' => 'Producto no encontrado'], 404);
    }



}
