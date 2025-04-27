<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class valoracionPotato extends Model
{
    use HasFactory;

    protected $table = 'valoraciones'; // nombre de la tabla

    protected $fillable = [
        'producto_id',
        'usuario_id',
        'puntuacion',
    ];

    // Relación: una valoración pertenece a un producto
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    // Relación: una valoración pertenece a un usuario
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
