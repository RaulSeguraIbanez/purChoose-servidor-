<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make('password123'),
            'role' => $this->faker->randomElement(['admin', 'usuario']),
            'ubicacion' => $this->faker->city(),
            'telefono' => $this->faker->phoneNumber(),
            'fotoPerfil' => '/storage/Images/userProfPic/user_profilepic_default.jpg',
        ];
    }
}
