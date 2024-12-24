<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sede>
 */
class SedeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        // Array con los valores que quieres insertar
        $sedes = ['Huancané', 'Rosaspata', 'Vilquechico'];

        // Seleccionamos aleatoriamente uno de los valores
        return [
            'nombre_sede' => $this->faker->randomElement($sedes),
        ];
    }
}
