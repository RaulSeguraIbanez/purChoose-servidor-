<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Historial extends Model
{
    use HasFactory;

    protected $table = 'historial';

    protected $fillable = [
        'user_id',
        'producto_id',
        'cantidad',
        'precio_total',
        'estado',
    ];

    protected static function booted()
    {
        static::created(function ($historial) {
            // Solo sumar ventas si el historial es de un producto pagado
            if ($historial->estado === 'pagado') {
                Producto::where('id', $historial->producto_id)
                    ->increment('ventas', $historial->cantidad);
            }
        });
    }

    // Relación con el usuario
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relación con el producto
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}