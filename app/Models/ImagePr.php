<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImagePr extends Model
{
    use HasFactory;
    protected $table = 'imagenes_pr';

    protected $fillable = ['url', 'producto_id'];

    // Relación con el producto
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}