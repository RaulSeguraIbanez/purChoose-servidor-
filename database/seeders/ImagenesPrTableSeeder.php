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
                ['url' => '/storage/images/productImages/img1.jpg', 'producto_id' => $producto->id],
                ['url' => '/storage/images/productImages/img2.jpg', 'producto_id' => $producto->id],
                ['url' => '/storage/images/productImages/img3.jpg', 'producto_id' => $producto->id],
            ]);
        });
    }
}
