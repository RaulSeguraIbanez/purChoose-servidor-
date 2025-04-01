<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Producto;
use App\Models\User;

class ProductosTableSeeder extends Seeder
{
    public function run()
    {
        Producto::factory(20)->create(); // Crea 20 productos aleatorios
    }
}
