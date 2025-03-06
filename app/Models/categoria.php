<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class categoria extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'descripcion', 'precio', 'estado', 'oferta', 'user_id'];

    public function categorias()
    {
        return $this->belongsToMany(Categoria::class, 'cate_prod');
    }
}
