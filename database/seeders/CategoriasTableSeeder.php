<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriasTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Lista de categorías con sus nombres e imágenes correspondientes
        $categorias = [
            ['nombre' => 'Tecnología y Electrónica', 'imagen' => 'echufCategory'],
            ['nombre' => 'Vehiculos', 'imagen' => 'carCategory'],
            ['nombre' => 'Moda y Accesorios', 'imagen' => 'clothesCategory'],
            ['nombre' => 'Hogar y Jardín', 'imagen' => 'home&gardenCategory'],
            ['nombre' => 'Deportes', 'imagen' => 'sportsCategory'],
            ['nombre' => 'Ocio', 'imagen' => 'ocioCategory'],
            ['nombre' => 'Inmobiliaria', 'imagen' => 'tableCategory'],
            ['nombre' => 'Niños y Bebés', 'imagen' => 'babyCategory'],
            ['nombre' => 'Coleccionables', 'imagen' => 'collectibleCategory'],
            ['nombre' => 'Construcción y Renovación', 'imagen' => 'constructionCategory'],
            ['nombre' => 'Industria y Agricultura', 'imagen' => 'agriculturaCategory'],
        ];

        // Insertar registros en la tabla 'categorias'
        foreach ($categorias as $categoria) {
            DB::table('categorias')->insert([
                'nombre' => $categoria['nombre'],
                'imagen' => 'http://localhost:8000/storage/Images/categoryImages/' . $categoria['imagen'] . '.png',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}