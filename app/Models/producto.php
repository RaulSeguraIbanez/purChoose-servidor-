<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array
     */
    protected $fillable = [
        'nombre',
        'descripcion',
        'precio',
        'estado',
        'ubicacion',
        'oferta',
        'individual',
        'user_id',
    ];

    /**
     * Los atributos que deben ocultarse al serializar el modelo.
     *
     * @var array
     */
    protected $hidden = [
        'quantity', // Asegúrate de que este campo exista en la tabla `productos`
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'precio' => 'float', // Asegura que el precio sea tratado como un número decimal
        'oferta' => 'boolean', // Convierte el campo `oferta` en un booleano
        'created_at' => 'datetime', // Formatea la fecha de creación
        'updated_at' => 'datetime', // Formatea la fecha de actualización
    ];

    /**
     * Relación muchos a muchos con el modelo Categoria.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function categorias()
    {
        return $this->belongsToMany(Categoria::class, 'cate_prod', 'producto_id', 'categoria_id');
    }

    /**
     * Relación uno a muchos con el modelo OpinionPotato (opiniones).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function opiniones()
    {
        return $this->hasMany(OpinionPotato::class, 'product_id');
    }

    /**
     * Relación uno a muchos con el modelo ImagenPr (imágenes del producto).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function imagenes()
    {
        return $this->hasMany(ImagePr::class, 'producto_id');
    }

    /**
     * Relación inversa con el modelo User (usuario que creó el producto).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}