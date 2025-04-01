<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class opinionPotato extends Model
{
    use HasFactory;

    protected $table = 'opinion_potato';

    protected $fillable = [
        'product_id',
        'user_id',
        'opinion',
    ];

    // Relaciones
    public function product()
    {
        return $this->belongsTo(Producto::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
