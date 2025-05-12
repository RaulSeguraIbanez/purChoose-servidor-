<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mensaje extends Model
{
    protected $fillable = ['chat_id', 'usuario_id', 'contenido'];

    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}