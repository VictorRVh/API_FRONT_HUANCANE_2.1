<?php

namespace Database\Seeders;

use App\Models\Sede;
use App\Models\Turno;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TurnoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Turno::factory()->create(['nombre_turno' => 'Mañana']);
        Turno::factory()->create(['nombre_turno' => 'Tarde']);
        Turno::factory()->create(['nombre_turno' => 'Noche']);
    }
}
