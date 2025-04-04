<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'descripcion', 'precio', 'estado', 'oferta', 'individual', 'user_id'];

    public function categorias()
    {
        return $this->belongsToMany(Categoria::class, 'cate_prod', 'producto_id', 'categoria_id');
    }
    protected $hidden = [
        'quantity'
    ];

    public function opiniones()
    {
        return $this->hasMany(opinionPotato::class, 'product_id');
    }
}
