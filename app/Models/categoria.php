<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;

    protected $fillable = ['nombre'];

    protected $hidden = ['created_at', 'updated_at', 'pivot'];

    public function productos()
    {
       return $this->belongsToMany(Producto::class, 'cate_prod', 'categoria_id', 'producto_id');
    }
}
