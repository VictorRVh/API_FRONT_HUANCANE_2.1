<?php

namespace App\Http\Controllers;

use App\Models\Matricula;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MatriculaController extends Controller
{
    public function index()
    {
        $matriculas = Matricula::with(['grupos', 'usuario'])->get();
        return response()->json($matriculas);
    }

    public function store(Request $request)
    {
        // Validación de datos
        $validator = Validator::make($request->all(), [
            'id_grupo' => 'required|exists:grupos,id_grupo',
            'id_estudiante' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        // Crear una nueva matricula
        $matricula = Matricula::create([
            'id_grupo' => $request->id_grupo,
            'id_estudiante' => $request->id_estudiante,
        ]);

        return response()->json([
            'message' => 'Matricula creada exitosamente',
            'matricula' => $matricula,
            'status' => 201
        ], 201);
    }

    public function show($id)
    {
        $matricula = Matricula::with(['grupos', 'usuario'])->find($id);

        if (!$matricula) {
            return response()->json([
                'message' => 'Matricula no encontrada',
                'status' => 404
            ], 404);
        }

        return response()->json($matricula);
    }

    public function update(Request $request, $id)
    {
        // Validación de datos
        $validator = Validator::make($request->all(), [
            'id_grupo' => 'required|exists:grupos,id_grupo',
            'id_estudiante' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        // Encontrar el registro existente
        $matricula = Matricula::find($id);

        if (!$matricula) {
            return response()->json([
                'message' => 'Matricula no encontrada',
                'status' => 404
            ], 404);
        }

        // Actualizar el registro
        $matricula->id_grupo = $request->id_grupo;
        $matricula->id_estudiante = $request->id_estudiante;
        $matricula->save();

        return response()->json([
            'matricula' => $matricula,
            'status' => 200
        ], 200);
    }

    public function destroy($id)
    {
        // Encontrar el registro existente
        $matricula = Matricula::find($id);

        if (!$matricula) {
            return response()->json([
                'message' => 'Matricula no encontrada',
                'status' => 404
            ], 404);
        }

        // Eliminar el registro
        $matricula->delete();

        return response()->json([
            'message' => 'Matricula eliminada exitosamente',
            'status' => 204
        ], 204);
    }

    public function getStudentById($dni)
    {
        $user = User::where('dni', $dni)
            ->whereHas('roles', function ($query) {
                $query->where('role_id', 8);
            })
            ->first();

        if (!$user) {
            return response()->json(['message' => 'Estudiante no encontrado'], 404);
        }

        $user->makeHidden(['created_at', 'updated_at']);

        // Retornar el usuario dentro de un array
        return response()->json([$user], 200);
    }

    public function getMatriculaPorPlanYEspecialidad($id_plan, $id_especialidad)
    {
        $matriculas = Matricula::with(['estudiante', 'grupos']) // Estos son datos de las funciones delos modelos
            ->whereHas('grupos', function ($query) use ($id_plan, $id_especialidad) {
                $query->where('id_plan', $id_plan)
                    ->where('id_especialidad', $id_especialidad);
            })
            ->get();

        $matriculas->each(function ($matricula) {
            $matricula->makeHidden(['created_at', 'updated_at']);

            if ($matricula->estudiante) { // Verifica si existe la relación antes de ocultar
                $matricula->estudiante->makeHidden(['created_at', 'updated_at']);
            }

            if ($matricula->grupos) { // Verifica si existe la relación antes de ocultar
                $matricula->grupos->makeHidden(['created_at', 'updated_at']);
            }
        });

        return response()->json($matriculas, 200);
    }

    public function getMatriculasPorPlanEspecialidadAndGrupo($id_plan, $id_especialidad, $id_grupo)
    {
        $matriculas = Matricula::with(['estudiante', 'grupos'])
            ->whereHas('grupos', function ($query) use ($id_plan, $id_especialidad, $id_grupo) {
                $query->where('id_plan', $id_plan)
                    ->where('id_especialidad', $id_especialidad);

                if ($id_grupo) {
                    $query->where('id_grupo', $id_grupo); // Filtra por grupo si se proporciona
                }
            })
            ->get();

        // Oculta los timestamps de los resultados
        $matriculas->each(function ($matricula) {
            $matricula->makeHidden(['created_at', 'updated_at']);

            if ($matricula->estudiante) {
                $matricula->estudiante->makeHidden(['created_at', 'updated_at']);
            }

            if ($matricula->grupos) {
                $matricula->grupos->makeHidden(['created_at', 'updated_at']);
            }
        });

        return response()->json($matriculas, 200);
    }
}
