<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'usuarios';

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

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'fechaRegistro' => 'datetime',
    ];

    // Asignar imagen por defecto si no se proporciona
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($user) {
            if (!$user->fotoPerfil) {
                $user->fotoPerfil = 'storage/images/userProfPic/user_profilepic_default.jpg';
            }
        });
    }
}
