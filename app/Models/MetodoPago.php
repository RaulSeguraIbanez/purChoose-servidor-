<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MetodoPago extends Model
{
    use HasFactory;

    // Nombre de la tabla
    protected $table = 'metodos_pago';
    protected $primaryKey = 'id_metodo';


    // Atributos que se pueden asignar masivamente
    protected $fillable = [
        'user_id',
        'tipo',
        'nombre',
        'num_tarjeta',
        'fecha_caducidad',
        'codigo_validacion',
        'email',
        'password',
    ];

    // Ocultar campos sensibles al serializar el modelo
    protected $hidden = [
        'num_tarjeta',
        'codigo_validacion',
        'password',
    ];

    /**
     * Relación con el modelo User.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}