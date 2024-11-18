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
            'roles-all',
            'roles-view',
            'roles-create',
            'roles-edit',
            'roles-delete',
            'permissions-all',
            'permissions-view',
            'permissions-create',
            'permissions-edit',
            'permissions-delete',
            
            ///Especialidades
            'specialties-all',
            'specialties-view',
            'specialties-create',
            'specialties-edit',
            'specialties-delete',

            'students-all',
            'students-view',
            'students-create',
            'students-edit',
            'students-delete',
            
            // profesores 
            'teachers-all',
            'teachers-view',
            'teachers-create',
            'teachers-edit',
            'teachers-delete',

            // permisos de plan 
            'plan-all',
            'plan-view',
            'plan-create',
            'plan-edit',
            'plan-delete',

            // Permisos porgrama
            'program-all',
            'program-view',
            'program-create',
            'program-edit',
            'program-delete',

            // unidades didacticas
            'units-all',
            'units-view',
            'units-create',
            'units-edit',
            'units-delete',

            // Indocador de logro indicators

            'indicators-all',
            'indicators-view',
            'indicators-create',
            'indicators-edit',
            'indicators-delete',

            // sededes 
            'places-all',
            'places-view',
            'places-create',
            'places-edit',
            'places-delete',
            
            // Grupos
            'groups-all',
            'groups-view',
            'groups-create',
            'groups-edit',
            'groups-delete',

            //Matricula estudiante 
            'enrollmentStudent-all',
            'enrollmentStudent-view',
            'enrollmentStudent-create',
            'enrollmentStudent-edit',
            'enrollmentStudent-delete',

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
