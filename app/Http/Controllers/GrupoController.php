<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use App\Models\Matricula;
use App\Models\Planes;
use App\Models\Programa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GrupoController extends Controller
{
    public function index()
    {
        $grupos = Grupo::with(['sede', 'turno', 'especialidad', 'plan', 'docente'])
            ->get()
            ->makeHidden(['created_at', 'updated_at']);

        // También oculta los timestamps en las relaciones
        $grupos->each(function ($grupo) {
            $grupo->sede->makeHidden(['created_at', 'updated_at']);
            $grupo->turno->makeHidden(['created_at', 'updated_at']);
            $grupo->especialidad->makeHidden(['created_at', 'updated_at']);
            $grupo->plan->makeHidden(['created_at', 'updated_at']);
            $grupo->programa->makeHidden(['created_at', 'updated_at']);
            $grupo->docente->makeHidden(['created_at', 'updated_at']);
        });

        return response()->json($grupos, 200);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validación
        $validator = Validator::make($request->all(), [
            'nombre_grupo' => 'required|string|max:255',
            'id_sede' => 'required|exists:sedes,id_sede',
            'id_turno' => 'required|exists:turnos,id',
            'id_especialidad' => 'required|exists:especialidades,id_especialidad',
            'id_plan' => 'required|exists:planes,id_plan',
            'id_programa' => 'required|exists:programas,id_programa',
            'id_docente' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors(),
                'status' => 400,
            ], 400);
        }

        // Crear el grupo
        $grupo = Grupo::create($request->all());

        return response()->json([
            'message' => 'Grupo creado exitosamente',
            'grupo' => $grupo,
            'status' => 201,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Incluye las relaciones asociadas
        $grupo = Grupo::with(['sede', 'turno', 'especialidad', 'plan', 'programa', 'docente'])->find($id);

        if (!$grupo) {
            return response()->json([
                'message' => 'Grupo no encontrado',
                'status' => 404,
            ], 404);
        }

        return response()->json($grupo, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $grupo = Grupo::find($id);

        if (!$grupo) {
            return response()->json([
                'message' => 'Grupo no encontrado',
                'status' => 404,
            ], 404);
        }

        // Validación
        $validator = Validator::make($request->all(), [
            'nombre_grupo' => 'string|max:255',
            'id_sede' => 'exists:sedes,id_sede',
            'id_turno' => 'exists:turnos,id',
            'id_especialidad' => 'exists:especialidades,id_especialidad',
            'id_plan' => 'exists:planes,id_plan',
            'id_programa' => 'required|exists:programas,id_programa',
            'id_docente' => 'exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors(),
                'status' => 400,
            ], 400);
        }

        // Actualizar el grupo
        $grupo->update($request->all());

        return response()->json([
            'message' => 'Grupo actualizado exitosamente',
            'grupo' => $grupo,
            'status' => 200,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $grupo = Grupo::find($id);

        if (!$grupo) {
            return response()->json([
                'message' => 'Grupo no encontrado',
                'status' => 404,
            ], 404);
        }

        // Eliminar el grupo
        $grupo->delete();

        return response()->json([
            'message' => 'Grupo eliminado exitosamente',
            'status' => 204,
        ], 204);
    }

    public function getGruposPorPlanYEspecialidad($id_plan, $id_especialidad)
    {
        $grupos = Grupo::with(['sede', 'turno', 'especialidad', 'plan', 'docente', 'programa'])
            ->where('id_plan', $id_plan)
            ->where('id_especialidad', $id_especialidad)
            ->get()
            ->makeHidden(['created_at', 'updated_at']);

        if ($grupos->isEmpty()) {
            return response()->json(['message' => 'No se encontraron grupos para el plan y especialidad especificados'], 404);
        }

        // Ocultar timestamps en relaciones
        $grupos->each(function ($grupo) {
            $grupo->sede->makeHidden(['created_at', 'updated_at']);
            $grupo->turno->makeHidden(['created_at', 'updated_at']);
            $grupo->especialidad->makeHidden(['created_at', 'updated_at']);
            $grupo->plan->makeHidden(['created_at', 'updated_at']);
            $grupo->programa->makeHidden(['created_at', 'updated_at']);
            $grupo->docente->makeHidden(['created_at', 'updated_at']);
        });

        return response()->json($grupos, 200);
    }

    public function getGruposPorUsuarioYPlan($usuario_id, $plan_id)
    {
        // Filtrar grupos por usuario y plan
        $grupos = Grupo::where('id_docente', $usuario_id)  // Filtrar por id_docente
            ->where('id_plan', $plan_id)                    // Filtrar por id_plan
            ->with(['sede', 'turno', 'plan', 'especialidad', 'programa']) // Cargar las relaciones necesarias
            ->get();

        $grupos->each(function ($grupo) {
            $grupo->makeHidden(['created_at', 'updated_at']);

            if ($grupo->sede) {
                $grupo->sede->makeHidden(['created_at', 'updated_at']);
            }

            if ($grupo->turno) {
                $grupo->turno->makeHidden(['created_at', 'updated_at']);
            }

            if ($grupo->plan) {
                $grupo->plan->makeHidden(['created_at', 'updated_at']);
            }

            if ($grupo->especialidad) {
                $grupo->especialidad->makeHidden(['created_at', 'updated_at']);
            }

            if ($grupo->programa) {
                $grupo->programa->makeHidden(['created_at', 'updated_at']);
            }

            if ($grupo->docente) {
                $grupo->docente->makeHidden(['created_at', 'updated_at']);
            }
        });

        return response()->json($grupos, 200);
    }

    public function getAlumnosPorGrupoYDocente($docente_id, $grupo_id)
    {
        $alumnos = Matricula::whereHas('grupos', function ($query) use ($docente_id, $grupo_id) {
            $query->where('id_docente', $docente_id)
                ->where('id_grupo', $grupo_id);
        })
            ->with('estudiante')
            ->get();

        $alumnos->each(function ($matricula) {
            $matricula->makeHidden(['created_at', 'updated_at']); 
            if ($matricula->estudiante) {
                $matricula->estudiante->makeHidden(['created_at', 'updated_at', 'email_verified_at']); 
            }
        });

        return response()->json($alumnos, 200);
    }
}
