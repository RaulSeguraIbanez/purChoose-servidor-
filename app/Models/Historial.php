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
            // Solo restar si el estado es pagado
            if ($historial->estado === 'pagado') {
                $producto = Producto::find($historial->producto_id);

                if ($producto) {
                    // Restar la cantidad vendida
                    $producto->quantity -= $historial->cantidad;

                    // Si el stock se agota, marcarlo como inactivo
                    if ($producto->quantity <= 0) {
                        $producto->activo = false;
                    }

                    $producto->save();
                }
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