<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categoria;

class CategoriasTableSeeder extends Seeder
{
    public function run()
    {
        Categoria::factory(5)->create(); // Crea 5 categorías aleatorias
    }
}
