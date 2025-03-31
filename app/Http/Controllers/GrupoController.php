<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use App\Models\Matricula;
use App\Models\Planes;
use App\Models\Programa;
use App\Models\Sede;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    public function getGruposPorEstudianteYPlan($estudiante_id, $plan_id)
    {
        // Obtener los ID de los grupos en los que está matriculado el estudiante
        $gruposIds = Matricula::where('id_estudiante', $estudiante_id)
            ->pluck('id_grupo'); // Extraer solo los IDs de los grupos

        // Obtener los grupos con sus relaciones
        $grupos = Grupo::whereIn('id_grupo', $gruposIds) // Filtrar por los grupos obtenidos
            ->where('id_plan', $plan_id) // Filtrar por el plan
            ->with(['sede', 'turno', 'plan', 'especialidad', 'programa']) // Cargar relaciones necesarias
            ->get();

        // Ocultar timestamps
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


    public function getAlumnosYUnidadesPorGrupo($grupo_id)
    {
        $alumnos = Matricula::whereHas('grupos', function ($query) use ($grupo_id) {
            $query->where('id_grupo', $grupo_id);
        })
            ->with([
                'estudiante.notas' => function ($query) use ($grupo_id) {
                    $query->where('id_grupo', $grupo_id);
                },
                'estudiante.notas.unidadDidactica'
            ])
            ->get();

        $grupo = Grupo::with('programa.unidadesDidacticas')->find($grupo_id);

        $alumnos->each(function ($matricula) {
            $matricula->makeHidden(['created_at', 'updated_at']);
            if ($matricula->estudiante) {
                $matricula->estudiante->makeHidden(['created_at', 'updated_at', 'email_verified_at']);
            }
        });

        // Obtener y ordenar las unidades didácticas del grupo
        $unidadesDidacticas = $grupo && $grupo->programa ? $grupo->programa->unidadesDidacticas->sortBy('numero_unidad')->values() : collect();
        // Filtrar unidades didácticas que no tienen notas registradas
        $unidadesConNotas = $alumnos->pluck('estudiante.notas.*.id_unidad_didactica')->flatten()->unique();
        $unidadesSinNotas = $unidadesDidacticas->whereNotIn('id_unidad_didactica', $unidadesConNotas);

        // Calcular el total de unidades didácticas
        $totalUnidades = $unidadesDidacticas->count();

        // Preparar la respuesta
        $response = [
            'estudiantes' => $alumnos->map(function ($matricula) {
                return [
                    'id' => $matricula->id_matricula,
                    'estudiante' => [
                        'id' => $matricula->estudiante->id,
                        'name' => $matricula->estudiante->name,
                        'apellido_paterno' => $matricula->estudiante->apellido_paterno,
                        'apellido_materno' => $matricula->estudiante->apellido_materno,
                        'dni' => $matricula->estudiante->dni,
                        'sexo' => $matricula->estudiante->sexo,
                        'celular' => $matricula->estudiante->celular,
                        'fecha_nacimiento' => $matricula->estudiante->fecha_nacimiento,
                        'email' => $matricula->estudiante->email,
                        'notas' => $matricula->estudiante->notas->map(function ($nota) {
                            return [
                                'id_nota' => $nota->id_nota,
                                'nota' => $nota->nota,
                                'id_unidad_didactica' => $nota->id_unidad_didactica,
                                'nombre_unidad' => $nota->unidadDidactica->nombre_unidad ?? null,
                                'id_grupo' => $nota->id_grupo,
                            ];
                        }),
                    ],
                ];
            }),
            'unidades_didacticas' => $unidadesSinNotas->makeHidden(['created_at', 'updated_at']),
            'total_unidades' => $totalUnidades,
        ];

        return response()->json($response, 200);
    }


    public function getEstudiantesYUnidadesPorGrupo($grupo_id)
    {
        $alumnos = Matricula::whereHas('grupos', function ($query) use ($grupo_id) {
            $query->where('id_grupo', $grupo_id);
        })
            ->with('estudiante')
            ->get();

        $grupo = Grupo::with('programa.unidadesDidacticas')
            ->find($grupo_id);

        $alumnos->each(function ($matricula) {
            $matricula->makeHidden(['created_at', 'updated_at']);
            if ($matricula->estudiante) {
                $matricula->estudiante->makeHidden(['created_at', 'updated_at', 'email_verified_at']);
            }
        });

        if ($grupo && $grupo->programa) {
            $especialidad = $grupo->especialidad->makeHidden(['created_at', 'updated_at']);
            $programa = $grupo->programa->nombre_programa;
            $turno = $grupo->turno->nombre_turno;
            $unidadesDidacticas = $grupo->programa->unidadesDidacticas->makeHidden(['created_at', 'updated_at']);
            $fecha_inicio_unidad = $unidadesDidacticas->min('fecha_inicio');
            $fecha_final_unidad = $unidadesDidacticas->max('fecha_fin');
        } else {
            $unidadesDidacticas = [];
        }

        $response = [
            'estudiantes' => $alumnos,
            'unidades_didacticas' => $unidadesDidacticas,
            'especialidad' => $especialidad,
            'programa' => $programa,
            'turno' => $turno,
            'fecha_inicio' => $fecha_inicio_unidad,
            'fecha_fin' => $fecha_final_unidad,
        ];

        return response()->json($response, 200);
    }

    public function getExperienciaYEstudiantesPorGrupo($grupo_id)
    {
        $alumnos = Matricula::whereHas('grupos', function ($query) use ($grupo_id) {
            $query->where('id_grupo', $grupo_id);
        })
            ->with('estudiante')
            ->get();

        $grupo = Grupo::with('programa.experienciasFormativas')
            ->find($grupo_id);

        $alumnos->each(function ($matricula) {
            $matricula->makeHidden(['created_at', 'updated_at']);
            if ($matricula->estudiante) {
                $matricula->estudiante->makeHidden(['created_at', 'updated_at', 'email_verified_at']);
            }
        });

        if ($grupo && $grupo->programa) {
            $experienciaFormativa = $grupo->programa->experienciasFormativas->makeHidden(['created_at', 'updated_at']);
        } else {
            $experienciaFormativa = [];
        }

        $response = [
            'estudiantes' => $alumnos,
            'experiencia_formativa' => $experienciaFormativa
        ];

        return response()->json($response, 200);
    }

    public function getNotasYExperienciasPorGrupo($grupo_id)
    {
        // Obtener los alumnos que pertenecen al grupo
        $alumnos = Matricula::whereHas('grupos', function ($query) use ($grupo_id) {
            $query->where('id_grupo', $grupo_id);
        })
            ->with([
                'estudiante.notasExperienciaFormativa.experienciaFormativa'
            ])
            ->get();

        // Obtener el grupo y sus experiencias formativas
        $grupo = Grupo::with('programa.experienciasFormativas')->find($grupo_id);

        // Hacer algunos ajustes en las propiedades de los estudiantes
        $alumnos->each(function ($matricula) {
            $matricula->makeHidden(['created_at', 'updated_at']);
            if ($matricula->estudiante) {
                $matricula->estudiante->makeHidden(['created_at', 'updated_at', 'email_verified_at']);
            }
        });

        // Si el grupo existe y tiene un programa, obtener las experiencias formativas
        if ($grupo && $grupo->programa) {
            $experienciaFormativa = $grupo->programa->experienciasFormativas->makeHidden(['created_at', 'updated_at']);
        } else {
            $experienciaFormativa = [];
        }

        // Mapeo de experiencias formativas con la bandera 'nota_asignada' fuera de los estudiantes
        $experienciasConBandera = $grupo->programa->experienciasFormativas->map(function ($experiencia) use ($alumnos) {
            // Verificar si al menos un estudiante tiene una nota asignada para esta experiencia
            $notaAsignada = $alumnos->some(function ($matricula) use ($experiencia) {
                return $matricula->estudiante->notasExperienciaFormativa->contains(function ($nota) use ($experiencia) {
                    return $nota->experienciaFormativa->id_experiencia == $experiencia->id_experiencia;
                });
            });

            return [
                'id_experiencia' => $experiencia->id_experiencia,
                'nombre_experiencia' => $experiencia->nombre_experiencia,
                'nota_asignada' => $notaAsignada ? true : false,
            ];
        });

        $response = [
            'estudiantes' => $alumnos->map(function ($matricula) {
                return [
                    'id' => $matricula->id_matricula,
                    'estudiante' => [
                        'id' => $matricula->estudiante->id,
                        'name' => $matricula->estudiante->name,
                        'apellido_paterno' => $matricula->estudiante->apellido_paterno,
                        'apellido_materno' => $matricula->estudiante->apellido_materno,
                        'dni' => $matricula->estudiante->dni,
                        'sexo' => $matricula->estudiante->sexo,
                        'celular' => $matricula->estudiante->celular,
                        'fecha_nacimiento' => $matricula->estudiante->fecha_nacimiento,
                        'email' => $matricula->estudiante->email,
                        // Agregar solo las notas de las experiencias formativas
                        'notas_experiencia_formativa' => $matricula->estudiante->notasExperienciaFormativa->map(function ($notaExperiencia) {
                            return [
                                'id_nota_experiencia' => $notaExperiencia->id_nota,
                                'nota_experiencia' => $notaExperiencia->nota,
                                'nombre_experiencia' => $notaExperiencia->experienciaFormativa->nombre_experiencia,
                                'id_grupo' => $notaExperiencia->id_grupo,
                            ];
                        }),
                    ],
                ];
            }),
            'experiencia_formativa' => $experienciasConBandera
        ];

        return response()->json($response, 200);
    }

    public function getTotalAlumnosPorPeriodoYSede($periodo, $sede_id)
    {
        $reporte = Sede::select(
            'sedes.id_sede',
            'sedes.nombre_sede',
            'especialidades.id_especialidad',
            'especialidades.nombre_especialidad',
            'grupos.id_plan',
            DB::raw('COUNT(matriculas.id_matricula) AS total_alumnos')
        )
            ->join('grupos', 'grupos.id_sede', '=', 'sedes.id_sede')
            ->join('especialidades', 'grupos.id_especialidad', '=', 'especialidades.id_especialidad')
            ->join('matriculas', 'matriculas.id_grupo', '=', 'grupos.id_grupo')
            ->where('grupos.id_plan', $periodo)
            ->where('sedes.id_sede', $sede_id)
            ->groupBy(
                'sedes.id_sede',
                'sedes.nombre_sede',
                'especialidades.id_especialidad',
                'especialidades.nombre_especialidad',
                'grupos.id_plan'
            )
            ->get();

        return response()->json($reporte);
    }
}
