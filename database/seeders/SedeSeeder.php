<?php

namespace Database\Seeders;

use App\Models\Sede;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SedeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Sede::factory()->create(['nombre_sede' => 'Huancane']);
        Sede::factory()->create(['nombre_sede' => 'Rosaspata']);
        Sede::factory()->create(['nombre_sede' => 'Vilquechico']);
    }
}
