<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    // Especificamos la tabla si el nombre no es el plural estándar de 'users'
    protected $table = 'usuarios';

    /**
     * Los atributos que se pueden asignar de forma masiva.
     */
    protected $fillable = [
        'nombre',
        'email',
        'password',
        'role',
        'fechaRegistro',
        'ubicacion',
        'telefono',
        'fotoPerfil',
    ];

    /**
     * Los atributos que se deben ocultar para la serialización.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Cast de atributos.
     */
    protected $casts = [
        'fechaRegistro' => 'datetime',
    ];
}