<?php

namespace Database\Factories;

use App\Models\Turno;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Turno>
 */
class TurnoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Turno::class;

    public function definition()
    {
        // Array con los valores que quieres insertar
        $turnos = ['Mañana', 'Tarde', 'Noche'];

        // Seleccionamos aleatoriamente uno de los valores
        return [
            'nombre_turno' => $this->faker->randomElement($turnos),
        ];
    }
}
