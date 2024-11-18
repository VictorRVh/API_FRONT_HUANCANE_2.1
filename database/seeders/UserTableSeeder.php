<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $password = Hash::make('password');

        $users = [
            [
                'name' => 'Mr. Super Admin',
                'apellido_paterno' => 'Admin',
                'apellido_materno' => 'Uno',
                'dni' => '12345678',
                'sexo' => 'M',
                'celular' => '987654321',
                'fecha_nacimiento' => '1980-01-01',
                'email' => 'sadmin@sadmin.com',
                'password' => $password,
                'created_at' => now(),
            ],
            [
                'name' => 'Mr. Admin',
                'apellido_paterno' => 'Principal',
                'apellido_materno' => 'Dos',
                'dni' => '87654321',
                'sexo' => 'M',
                'celular' => '912345678',
                'fecha_nacimiento' => '1985-05-05',
                'email' => 'admin@admin.com',
                'password' => $password,
                'created_at' => now(),
            ],
            [
                'name' => 'Mr. Author',
                'apellido_paterno' => 'Escritor',
                'apellido_materno' => 'Tres',
                'dni' => '12348765',
                'sexo' => 'F',
                'celular' => '987123456',
                'fecha_nacimiento' => '1990-07-15',
                'email' => 'author@author.com',
                'password' => $password,
                'created_at' => now(),
            ],
            [
                'name' => 'Mr. Editor',
                'apellido_paterno' => 'Editor',
                'apellido_materno' => 'Cuatro',
                'dni' => '87651234',
                'sexo' => 'M',
                'celular' => '923456789',
                'fecha_nacimiento' => '1988-12-20',
                'email' => 'editor@editor.com',
                'password' => $password,
                'created_at' => now(),
            ],
            [
                'name' => 'Mr. User 1',
                'apellido_paterno' => 'Usuario',
                'apellido_materno' => 'Uno',
                'dni' => '98765432',
                'sexo' => 'F',
                'celular' => '956789432',
                'fecha_nacimiento' => '1992-11-11',
                'email' => 'user1@user.com',
                'password' => $password,
                'created_at' => now(),
            ],
            [
                'name' => 'Mr. User 2',
                'apellido_paterno' => 'Usuario',
                'apellido_materno' => 'Dos',
                'dni' => '98765411',
                'sexo' => 'F',
                'celular' => '956789432',
                'fecha_nacimiento' => '1992-11-11',
                'email' => 'user2@user.com',
                'password' => $password,
                'created_at' => now(),
            ],
            [
                'name' => 'Mr. User 3',
                'apellido_paterno' => 'Usuario',
                'apellido_materno' => 'Tres',
                'dni' => '98765100',
                'sexo' => 'F',
                'celular' => '956789432',
                'fecha_nacimiento' => '1992-11-11',
                'email' => 'user3@user.com',
                'password' => $password,
                'created_at' => now(),
            ],
            [
                'name' => 'Mr. User 4',
                'apellido_paterno' => 'Usuario',
                'apellido_materno' => 'Cuatro',
                'dni' => '98762100',
                'sexo' => 'F',
                'celular' => '956789432',
                'fecha_nacimiento' => '1992-11-11',
                'email' => 'user4@user.com',
                'password' => $password,
                'created_at' => now(),
            ],
            [
                'name' => 'Mr. User 5',
                'apellido_paterno' => 'Usuario',
                'apellido_materno' => 'Cinco',
                'dni' => '98765439',
                'sexo' => 'F',
                'celular' => '956789432',
                'fecha_nacimiento' => '1992-11-11',
                'email' => 'user@user.com',
                'password' => $password,
                'created_at' => now(),
            ],
            [
                'name' => 'Victor',
                'apellido_paterno' => 'Usuario',
                'apellido_materno' => 'Vvictor',
                'dni' => '73638061',
                'sexo' => 'F',
                'celular' => '956789432',
                'fecha_nacimiento' => '1992-11-11',
                'email' => 'uservictor@user.com',
                'password' => $password,
                'created_at' => now(),
            ],
            [
                'name' => 'Leonardo',
                'apellido_paterno' => 'Usuario',
                'apellido_materno' => 'Leonarod',
                'dni' => '73638912',
                'sexo' => 'F',
                'celular' => '956789432',
                'fecha_nacimiento' => '1992-11-11',
                'email' => 'userleo@user.com',
                'password' => $password,
                'created_at' => now(),
            ],
        ];

        DB::table('users')->insert($users);
    }
}
