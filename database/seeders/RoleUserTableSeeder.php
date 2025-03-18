<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleUserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener usuarios reales por su orden de creación o por algún campo único (ej: email)
        $users = \App\Models\User::orderBy('created_at')->take(11)->get();

        $roleUser = [
            [
                'role_id' => 1,
                'user_id' => $users[0]->id,
                'created_at' => now(),
            ],
            [
                'role_id' => 2,
                'user_id' => $users[1]->id,
                'created_at' => now(),
            ],
            [
                'role_id' => 3,
                'user_id' => $users[2]->id,
                'created_at' => now(),
            ],
            [
                'role_id' => 4,
                'user_id' => $users[3]->id,
                'created_at' => now(),
            ],
            [
                'role_id' => 5,
                'user_id' => $users[4]->id,
                'created_at' => now(),
            ],
            [
                'role_id' => 6,
                'user_id' => $users[5]->id,
                'created_at' => now(),
            ],
            [
                'role_id' => 7,
                'user_id' => $users[6]->id,
                'created_at' => now(),
            ],
            [
                'role_id' => 7,
                'user_id' => $users[7]->id,
                'created_at' => now(),
            ],
            [
                'role_id' => 8,
                'user_id' => $users[8]->id,
                'created_at' => now(),
            ],
            [
                'role_id' => 8,
                'user_id' => $users[9]->id,
                'created_at' => now(),
            ],
            [
                'role_id' => 8,
                'user_id' => $users[10]->id,
                'created_at' => now(),
            ],
        ];

        DB::table('role_user')->insert($roleUser);
    }
}
