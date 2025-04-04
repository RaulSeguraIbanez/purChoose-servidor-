<?php

namespace Database\Factories;

use App\Models\Producto;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductoFactory extends Factory
{
    protected $model = Producto::class;

    public function definition(): array
    {
        return [
            'nombre' => $this->faker->word(),
            'descripcion' => $this->faker->sentence(),
            'precio' => $this->faker->randomFloat(2, 5, 1000),
            'estado' => $this->faker->randomElement(['Nuevo', 'Usado', 'Reacondicionado']),
            'oferta' => $this->faker->boolean(),
            'individual' => $this->faker->boolean(),
            'user_id' => User::factory(),
        ];
    }
}
