<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'users-all',
            'users-view',
            'users-create',
            'users-edit',
            'users-delete',
            'users-icon',


            'roles-all',
            'roles-view',
            'roles-create',
            'roles-edit',
            'roles-delete',
            'roles-icon',


            'permissions-all',
            'permissions-view',
            'permissions-create',
            'permissions-edit',
            'permissions-delete',
            'permissions-icon',
            
            ///Especialidades
            'specialties-all',
            'specialties-view',
            'specialties-create',
            'specialties-edit',
            'specialties-delete',
            'specialties-icon',

            'students-all',
            'students-view',
            'students-create',
            'students-edit',
            'students-delete',
            'students-icon',
            
            // profesores 
            'teachers-all',
            'teachers-view',
            'teachers-create',
            'teachers-edit',
            'teachers-delete',
            'teachers-icon',

            // permisos de plan 
            'plan-all',
            'plan-view',
            'plan-create',
            'plan-edit',
            'plan-delete',
            'plan-icon',

            // Permisos porgrama
            'program-all',
            'program-view',
            'program-create',
            'program-edit',
            'program-delete',
            'program-icon',

            // unidades didacticas
            'units-all',
            'units-view',
            'units-create',
            'units-edit',
            'units-delete',
            //'units-icon',

            // Indocador de logro indicators

            'indicators-all',
            'indicators-view',
            'indicators-create',
            'indicators-edit',
            'indicators-delete',
            //'indicators-delete',

            // sededes 
            'places-all',
            'places-view',
            'places-create',
            'places-edit',
            'places-delete',
            'places-icon',
            
            // Grupos
            'groups-all',
            'groups-view',
            'groups-create',
            'groups-edit',
            'groups-delete',
            'groups-icon',

            //Matricula estudiante 
            'enrollmentStudent-all',
            'enrollmentStudent-view',
            'enrollmentStudent-create',
            'enrollmentStudent-edit',
            'enrollmentStudent-delete',
            'enrollmentStudent-icon',

            //Notas estudiante 
            'note-all',
            'note-view',
            'note-create',
            'note-edit',
            'note-delete',
            'note-icon',

            //Permiso para estudiantes
            'note-student-all',
            'note-student-view',
            'note-student-create',
            'note-student-edit',
            'note-student-delete',
            'note-student-icon',
        ];

        $permissions = array_map(function ($name) {
            return [
                'name' => $name,
                'created_at' => now(),
            ];
        }, $permissions);

        DB::table('permissions')->insert($permissions);
    }
}
