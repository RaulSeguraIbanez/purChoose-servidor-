<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Categoria;
use App\Models\Producto;

class CateProdTableSeeder extends Seeder
{
    public function run()
    {
        $categorias = Categoria::all();

        Producto::all()->each(function ($producto) use ($categorias) {
            $categoriaIds = $categorias->random(rand(1, 3))->pluck('id')->toArray();

            foreach ($categoriaIds as $categoriaId) {
                DB::table('cate_prod')->insert([
                    'categoria_id' => $categoriaId,
                    'producto_id' => $producto->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });
    }
}
