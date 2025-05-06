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
                ['url' => '/storage/app/public/Images/productImages/img1.jpg', 'producto_id' => $producto->id],
                ['url' => '/storage/app/public/Images/productImages/img2.jpg', 'producto_id' => $producto->id],
                ['url' => '/storage/app/public/Images/productImages/img3.jpg', 'producto_id' => $producto->id],
            ]);
        });
    }
}
