<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UsersTableSeeder::class,
            CategoriasTableSeeder::class,
            ProductosTableSeeder::class,
            ImagenesPrTableSeeder::class,  // Inserción manual de imágenes
            CateProdTableSeeder::class,    // Inserción manual de relaciones N:M
        ]);
    }
}
