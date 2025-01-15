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

    public function generarReporteMatricula($dni)
    {
        $matricula = Matricula::with([
            'estudiante',
            'grupos.programa.unidadesDidacticas'
        ])->whereHas('estudiante', function ($query) use ($dni) {
            $query->where('id_estudiante', $dni);
        })->get();


        $response = $matricula->map(function ($registro) {
            return [
                'especialidad' => $registro->grupos->especialidad->nombre_especialidad,  // Accede desde grupos
                'periodo_academico' => $registro->grupos->plan->nombre_plan,
                'programa' => $registro->grupos->programa->nombre_programa,
                'periodo_clase' => optional($registro->grupos->programa->unidadesDidacticas->sortBy('fecha_inicio')->first())->fecha_inicio
                    . ' al ' .
                    optional($registro->grupos->programa->unidadesDidacticas->sortByDesc('fecha_final')->first())->fecha_fin,
                'dni' => $registro->estudiante->dni,
                'apellidos_nombres' => $registro->estudiante->apellido_paterno . ' ' . $registro->estudiante->apellido_materno . ', ' . strtoupper($registro->estudiante->name),
                'unidades_didacticas' => $registro->grupos->programa->unidadesDidacticas->map(function ($unidad, $index) {
                    return [
                        'numero' => str_pad($index + 1, 2, '0', STR_PAD_LEFT),
                        'nombre_unidad' => $unidad->nombre_unidad,
                        'credito' => $unidad->creditos,
                        'hora' => $unidad->horas,
                        'condicion' => 'G'
                    ];
                }),
            ];
        });


        return response()->json($response, 200);
    }

    public function generarReporteCertificado($dni)
    {
        $matricula = Matricula::with([
            'estudiante',
            'grupos.programa.unidadesDidacticas.notas',
            'grupos.programa.experienciasFormativas.notas'
        ])->whereHas('estudiante', function ($query) use ($dni) {
            $query->where('id_estudiante', $dni);
        })->get();

        $response = $matricula->map(function ($registro) {
            return [
                'especialidad' => $registro->grupos->especialidad->nombre_especialidad,
                'periodo_academico' => $registro->grupos->plan->nombre_plan,
                'programa' => $registro->grupos->programa->nombre_programa,
                'unidad_competencia' => $registro->grupos->programa->unidades_competencia,
                'periodo_clase' => optional($registro->grupos->programa->unidadesDidacticas->sortBy('fecha_inicio')->first())->fecha_inicio
                    . ' al ' .
                    optional($registro->grupos->programa->unidadesDidacticas->sortByDesc('fecha_final')->first())->fecha_fin,
                'dni' => $registro->estudiante->dni,
                'apellidos_nombres' => $registro->estudiante->apellido_paterno . ' ' . $registro->estudiante->apellido_materno . ', ' . strtoupper($registro->estudiante->name),
                'unidades_didacticas' => $registro->grupos->programa->unidadesDidacticas->map(function ($unidad, $index) use ($registro) {
                    $nota = $unidad->notas->firstWhere('id_estudiante', $registro->estudiante->id);
                    return [
                        'numero' => str_pad($index + 1, 2, '0', STR_PAD_LEFT),
                        'nombre_unidad' => $unidad->nombre_unidad,
                        'credito' => $unidad->creditos,
                        'hora' => $unidad->horas,
                        'condicion' => $nota ? 'Aprobado' : 'Pendiente', // Cambia según tu lógica
                        'nota' => $nota ? $nota->nota : 'N/A', // Traer la nota registrada
                        'capacidad' => $unidad->capacidad
                    ];
                }),
                'experiencias_formativas' => $registro->grupos->programa->experienciasFormativas->map(function ($experiencia) use ($registro) {
                    $nota = $experiencia->notas->firstWhere('id_estudiante', $registro->estudiante->id);
                    return [
                        'nombre_experiencia' => $experiencia->nombre_experiencia,
                        // 'descripcion' => $experiencia->descripcion,
                        'creditos_exp' => $experiencia->creditos,
                        'horas_exp' => $experiencia->horas,
                        'nota' => $nota ? $nota->nota : 'N/A'
                    ];
                }),
            ];
        });

        return response()->json($response, 200);
    }
}
