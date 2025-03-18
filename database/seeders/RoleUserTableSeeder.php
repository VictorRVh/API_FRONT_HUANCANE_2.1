<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class RoleUserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Definir los correos con su respectivo role_id
        $roleAssignments = [
            'superadmin@user.com'       => 1,
            'admin@user.com'       => 2,
            'director@user.com'    => 2,
            'coordinador@user.com' => 3,
            'secretaria@user.com'  => 4,
            'docente1@user.com'    => 5,
            'docente2@user.com'    => 6,
            'docente3@user.com'    => 7,
            'docente4@user.com'    => 7,
            'alumno1@user.com'  => 8,
            'alumno2@user.com'  => 8,
            'alumno3@user.com'  => 8,
            'alumno4@user.com'  => 8,
            'alumno5@user.com'  => 8,
            'alumno6@user.com'  => 8,
            'alumno7@user.com'  => 8,
            'alumno8@user.com'  => 8,
            'alumno9@user.com'  => 8,
            'alumno10@user.com' => 8,
            'alumno11@user.com' => 8,
            'alumno12@user.com' => 8,
            'alumno13@user.com' => 8,
            'alumno14@user.com' => 8,
            'alumno15@user.com' => 8,
            'alumno16@user.com' => 8,
            'alumno17@user.com' => 8,
            'alumno18@user.com' => 8,
            'alumno19@user.com' => 8,
            'alumno20@user.com' => 8,
        ];

        $roleUser = [];

        foreach ($roleAssignments as $email => $roleId) {
            // Buscar el usuario por su correo electrónico
            $user = User::where('email', $email)->first();

            if ($user) {
                $roleUser[] = [
                    'role_id'    => $roleId,
                    'user_id'    => $user->id,
                    'created_at' => now(),
                ];
            } else {
                // Si el usuario no existe, muestra un mensaje en la consola
                $this->command->warn("Usuario con email {$email} no encontrado.");
            }
        }

        if (!empty($roleUser)) {
            DB::table('role_user')->insert($roleUser);
            $this->command->info('Roles asignados correctamente por email.');
        } else {
            $this->command->warn('No se encontraron usuarios para asignar roles.');
        }
    }
}
