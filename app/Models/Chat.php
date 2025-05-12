<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $fillable = ['producto_id', 'usuario1_id', 'usuario2_id'];

    public function mensajes()
    {
        return $this->hasMany(Mensaje::class);
    }

    public function usuario1()
    {
        return $this->belongsTo(User::class, 'usuario1_id');
    }

    public function usuario2()
    {
        return $this->belongsTo(User::class, 'usuario2_id');
    }
}