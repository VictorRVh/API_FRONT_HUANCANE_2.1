<?php

namespace App\Http\Controllers;

use App\Models\Nivel;
use Illuminate\Http\Request;

class NivelController extends Controller
{
     // Crear nuevo nivel al matricularse (semestre 1, activo = false)
     public function crearNivel($id_matricula)
     {
         $existeNivel = Nivel::where('id_matricula', $id_matricula)->exists();
 
         if ($existeNivel) {
             return response()->json(['mensaje' => 'Nivel ya existe para esta matrícula'], 400);
         }
 
         $nivel = Nivel::create([
             'id_matricula' => $id_matricula,
             'semestre' => 1,
             'activo' => false
         ]);
 
         return response()->json(['mensaje' => 'Nivel creado', 'nivel' => $nivel], 201);
     }
 
     // Culminar semestre actual y crear siguiente nivel
     public function avanzarNivel($id_matricula)
     {
         $nivelActual = Nivel::where('id_matricula', $id_matricula)
                             ->where('activo', false)
                             ->orderByDesc('semestre')
                             ->first();
 
         if (!$nivelActual) {
             return response()->json(['mensaje' => 'No hay nivel activo para esta matrícula'], 404);
         }
 
         // Actualizar nivel actual a activo = true (culminado)
         $nivelActual->update(['activo' => true]);
 
         // Crear nuevo nivel con semestre +1 y activo = false
         $nuevoNivel = Nivel::create([
             'id_matricula' => $id_matricula,
             'semestre' => $nivelActual->semestre + 1,
             'activo' => false
         ]);
 
         return response()->json(['mensaje' => 'Nivel actualizado y nuevo nivel creado', 'nivel' => $nuevoNivel], 201);
     }
}
