<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Producto;

class ImagenesPrTableSeeder extends Seeder
{
    public function run()
    {
        Producto::all()->each(function ($producto) {
            DB::table('imagenes_pr')->insert([
                ['url' => 'storage/productos/img1.jpg', 'producto_id' => $producto->id],
                ['url' => 'storage/productos/img2.jpg', 'producto_id' => $producto->id],
                ['url' => 'storage/productos/img3.jpg', 'producto_id' => $producto->id],
            ]);
        });
    }
}
