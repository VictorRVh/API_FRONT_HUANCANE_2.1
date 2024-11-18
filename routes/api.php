<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
 * ------------------------------------------------------------------------
 * auth routes
 * ------------------------------------------------------------------------
 */
Route::post('login', [\App\Http\Controllers\AuthController::class, 'login']);
Route::get('auth/verify', [
    \App\Http\Controllers\AuthController::class,
    'verify',
]);

Route::middleware('auth:sanctum')->group(function () {
    /**
     * ------------------------------------------------------------------------
     * common routes
     * ------------------------------------------------------------------------
     */
    Route::get('logout', [
        \App\Http\Controllers\AuthController::class,
        'logout',
    ]);
    Route::get('user', function (Request $request) {
        return $request->user();
    });

    /**
     * ------------------------------------------------------------------------
     * users routes
     * ------------------------------------------------------------------------
     */
    Route::get('users', [
        \App\Http\Controllers\UserController::class,
        'index',
    ])->middleware('permission:users-all|users-view');

    Route::post('users', [
        \App\Http\Controllers\UserController::class,
        'store',
    ])->middleware('permission:users-all|students-all|users-create');

    Route::patch('users/{userId}', [
        \App\Http\Controllers\UserController::class,
        'update',
    ])->middleware('permission:users-all|students-all|users-edit');

    Route::delete('users/{userId}', [
        \App\Http\Controllers\UserController::class,
        'destroy',
    ])->middleware('permission:users-all|users-delete');

    /**
     * ------------------------------------------------------------------------
     * roles routes
     * ------------------------------------------------------------------------
     */
    Route::get('roles', [
        \App\Http\Controllers\RoleController::class,
        'index',
    ])->middleware('permission:roles-all|roles-view');

    Route::post('roles', [
        \App\Http\Controllers\RoleController::class,
        'store',
    ])->middleware('permission:roles-all|roles-create');

    Route::patch('roles/{roleId}', [
        \App\Http\Controllers\RoleController::class,
        'update',
    ])->middleware('permission:roles-all|roles-edit');

    Route::delete('roles/{roleId}', [
        \App\Http\Controllers\RoleController::class,
        'destroy',
    ])->middleware('permission:roles-all|roles-delete');

    /**
     * ------------------------------------------------------------------------
     * permissions routes
     * ------------------------------------------------------------------------
     */
    Route::get('permissions', [
        \App\Http\Controllers\PermissionController::class,
        'index',
    ])->middleware('permission:permissions-all|permissions-view');

    Route::post('permissions', [
        \App\Http\Controllers\PermissionController::class,
        'store',
    ])->middleware('permission:permissions-all|permissions-create');

    Route::patch('permissions/{permissionId}', [
        \App\Http\Controllers\PermissionController::class,
        'update',
    ])->middleware('permission:permissions-all|permissions-edit');

    Route::delete('permissions/{permissionId}', [
        \App\Http\Controllers\PermissionController::class,
        'destroy',
    ])->middleware('permission:permissions-all|permissions-delete');


    // RUTA DE SEDES

    Route::get('sedes', [
        \App\Http\Controllers\SedeController::class,
        'index',
    ])->middleware('permission:places-all|places-view');

    Route::post('sedes', [
        \App\Http\Controllers\SedeController::class,
        'store',
    ])->middleware('permission:places-all|places-create');

    Route::patch('sedes/{sedeId}', [
        \App\Http\Controllers\SedeController::class,
        'update',
    ])->middleware('permission:places-all|places-edit');

    Route::delete('sedes/{sedeId}', [
        \App\Http\Controllers\SedeController::class,
        'destroy',
    ])->middleware('permission:places-all|places-delete');

        
    // RUTAS DE TURNOS

    Route::get('turno', [
        \App\Http\Controllers\TurnoController::class,
        'index',
    ])->middleware('permission:permissions-all|permissions-view');

    Route::post('turno', [
        \App\Http\Controllers\TurnoController::class,
        'store',
    ])->middleware('permission:permissions-all|permissions-create');

    Route::patch('turno/{sedeId}', [
        \App\Http\Controllers\TurnoController::class,
        'update',
    ])->middleware('permission:permissions-all|permissions-edit');

    Route::delete('turno/{sedeId}', [
        \App\Http\Controllers\TurnoController::class,
        'destroy',
    ])->middleware('permission:permissions-all|permissions-delete');


    // RUTAS DE ESPECIALIDADES

    Route::get('especialidad', [
        \App\Http\Controllers\EspecialidadController::class,
        'index',
    ])->middleware('permission:specialties-all|specialties-view');
    
    Route::get('especialidad/{especialidadId}', [
        \App\Http\Controllers\EspecialidadController::class,
        'show',
    ])->middleware('permission:specialties-all|specialties-view');

    Route::post('especialidad', [
        \App\Http\Controllers\EspecialidadController::class,
        'store',
    ])->middleware('permission:specialties-all|specialties-create');

    Route::patch('especialidad/{especialidadId}', [
        \App\Http\Controllers\EspecialidadController::class,
        'update',
    ])->middleware('permission:specialties-all|specialties-edit');

    Route::delete('especialidad/{especialidadId}', [
        \App\Http\Controllers\EspecialidadController::class,
        'destroy',
    ])->middleware('permission:specialties-all|specialties-delete');

    
    // RUTA PARA PLANES

    Route::get('plan', [
        \App\Http\Controllers\PlanController::class,
        'index',
    ])->middleware('permission:plan-all|plan-view');

    Route::get('planEspecialidad/{id}', [
        \App\Http\Controllers\PlanController::class,
        'getEspecialidadPrograma',
    ])->middleware('permission:plan-all|plan-view');

    Route::post('plan', [
        \App\Http\Controllers\PlanController::class,
        'store',
    ])->middleware('permission:plan-all|plan-create');

    Route::patch('plan/{planId}', [
        \App\Http\Controllers\PlanController::class,
        'update',
    ])->middleware('permission:plan-all|plan-edit');

    Route::delete('plan/{planId}', [
        \App\Http\Controllers\PlanController::class,
        'destroy',
    ])->middleware('permission:plan-all|plan-delete');


    // RUTA PARA PROGRAMAS

    Route::get('programa', [
        \App\Http\Controllers\ProgramaController::class,
        'index',
    ])->middleware('permission:program-all|program-view');

    Route::get('programa/{id_especialidad}/{id_plan}', [
        \App\Http\Controllers\PlanController::class,
        'getEspecialidadPrograma',
    ])->middleware('permission:program-all|plan-delete');

    Route::post('programa', [
        \App\Http\Controllers\ProgramaController::class,
        'store',
    ])->middleware('permission:program-all|program-create');

    Route::patch('programa/{programaId}', [
        \App\Http\Controllers\ProgramaController::class,
        'update',
    ])->middleware('permission:program-all|program-edit');

    Route::delete('programa/{programaId}', [
        \App\Http\Controllers\ProgramaController::class,
        'destroy',
    ])->middleware('permission:program-all|program-delete');

    
    // RUTA PARA UNIDADES DIDACTICAS

    Route::get('unidad_didactica', [
        \App\Http\Controllers\UnidadDidacticaController::class,
        'index',
    ])->middleware('permission:units-all|units-view');

    Route::get('unidad_didactica/{id_programa}', [
        \App\Http\Controllers\UnidadDidacticaController::class,
        'getUnidadDidacticaPrograma',
    ])->middleware('permission:units-all|units-view');

    Route::post('unidad_didactica', [
        \App\Http\Controllers\UnidadDidacticaController::class,
        'store',
    ])->middleware('permission:units-all|units-create');

    Route::patch('unidad_didactica/{unidadId}', [
        \App\Http\Controllers\UnidadDidacticaController::class,
        'update',
    ])->middleware('permission:units-all|units-edit');

    Route::delete('unidad_didactica/{unidadId}', [
        \App\Http\Controllers\UnidadDidacticaController::class,
        'destroy',
    ])->middleware('permission:units-all|units-delete');


    // RUTA PARA INDICADOR DE LOGRO

    Route::get('indicador_logro', [
        \App\Http\Controllers\IndicadorLogroController::class,
        'index',
    ])->middleware('permission:indicators-all|indicators-view');

    Route::get('indicador_logro/{id_unidad_didactica}', [
        \App\Http\Controllers\IndicadorLogroController::class,
        'getIndicadoresPorUnidadDidactica',
    ])->middleware('permission:indicators-all|indicators-view');

    Route::post('indicador_logro', [
        \App\Http\Controllers\IndicadorLogroController::class,
        'store',
    ])->middleware('permission:indicators-all|indicators-create');

    Route::patch('indicador_logro/{indicadorId}', [
        \App\Http\Controllers\IndicadorLogroController::class,
        'update',
    ])->middleware('permission:indicators-all|indicators-edit');

    Route::delete('indicador_logro/{indicadorId}', [
        \App\Http\Controllers\IndicadorLogroController::class,
        'destroy',
    ])->middleware('permission:indicators-all|indicators-delete');


    // RUTA PARA EXPERIENCIAS FORMATIVAS

    Route::get('experiencia_formativa', [
        \App\Http\Controllers\ExperienciaFormativaController::class,
        'index',
    ])->middleware('permission:permissions-all|permissions-view');

    Route::get('experiencia_formativa/{id_programa}', [
        \App\Http\Controllers\ExperienciaFormativaController::class,
        'getExperienciaFormativaPrograma',
    ])->middleware('permission:permissions-all|permissions-view');

    Route::post('experiencia_formativa', [
        \App\Http\Controllers\ExperienciaFormativaController::class,
        'store',
    ])->middleware('permission:permissions-all|permissions-create');

    Route::patch('experiencia_formativa/{experienciaId}', [
        \App\Http\Controllers\ExperienciaFormativaController::class,
        'update',
    ])->middleware('permission:permissions-all|permissions-edit');

    Route::delete('experiencia_formativa/{experienciaId}', [
        \App\Http\Controllers\ExperienciaFormativaController::class,
        'destroy',
    ])->middleware('permission:permissions-all|permissions-delete');


    // RUTA PARA GRUPOS

    Route::get('grupo', [
        \App\Http\Controllers\GrupoController::class,
        'index',
    ])->middleware('permission:groups-all|groups-view');

    Route::get('grupo/{id_plan}/{id_especialidad}', [
        \App\Http\Controllers\GrupoController::class,
        'getGruposPorPlanYEspecialidad',
    ])->middleware('permission:groups-all|groups-view');

    //RUTA DE GRUPO POR DOCENTE Y PLAN

    Route::get('grupoDocente/{usuario_id}/{plan_id}', [
        \App\Http\Controllers\GrupoController::class,
        'getGruposPorUsuarioYPlan',
    ])->middleware('permission:groups-all|groups-view');
    
    //RUTA DE ESTUDIANTE POR GRUPO Y DOCENTE 

    Route::get('grupoEstudiante/{docente_id}/{grupo_id}', [
        \App\Http\Controllers\GrupoController::class,
        'getAlumnosPorGrupoYDocente',
    ])->middleware('permission:groups-all|groups-view');

    Route::post('grupo', [
        \App\Http\Controllers\GrupoController::class,
        'store',
    ])->middleware('permission:groups-all|groups-create');

    Route::patch('grupo/{grupoId}', [
        \App\Http\Controllers\GrupoController::class,
        'update',
    ])->middleware('permission:groups-all|groups-edit');

    Route::delete('grupo/{grupoId}', [
        \App\Http\Controllers\GrupoController::class,
        'destroy',
    ])->middleware('permission:groups-all|groups-delete');


    // RUTA PARA MATRICULAS

    Route::get('matricula', [
        \App\Http\Controllers\MatriculaController::class,
        'index',
    ])->middleware('permission:enrollmentStudent-all|enrollmentStudent-view');

    Route::get('matricula/{dni}', [
        \App\Http\Controllers\MatriculaController::class,
        'getStudentById',
    ])->middleware('permission:enrollmentStudent-all|enrollmentStudent-view');

    Route::get('matricula/{id_plan}/{id_especialidad}', [
        \App\Http\Controllers\MatriculaController::class,
        'getMatriculaPorPlanYEspecialidad',
    ])->middleware('permission:enrollmentStudent-all|enrollmentStudent-view');

    Route::get('matricula/{id_plan}/{id_especialidad}/{id_grupo}', [
        \App\Http\Controllers\MatriculaController::class,
        'getMatriculasPorPlanEspecialidadAndGrupo',
    ])->middleware('permission:enrollmentStudent-all|enrollmentStudent-view');

    Route::post('matricula', [
        \App\Http\Controllers\MatriculaController::class,
        'store',
    ])->middleware('permission:enrollmentStudent-all|enrollmentStudent-create');

    Route::patch('matricula/{matriculaId}', [
        \App\Http\Controllers\MatriculaController::class,
        'update',
    ])->middleware('permission:enrollmentStudent-all|enrollmentStudent-edit');

    Route::delete('matricula/{matriculaId}', [
        \App\Http\Controllers\MatriculaController::class,
        'destroy',
    ])->middleware('permission:enrollmentStudent-all|enrollmentStudent-delete');


    // RUTA PARA NOTAS DE UNIDADES DIDACTICAS

    Route::get('nota_unidad_didactica', [
        \App\Http\Controllers\NotaUnidadDidacticaController::class,
        'index',
    ])->middleware('permission:permissions-all|permissions-view');

    Route::post('nota_unidad_didactica', [
        \App\Http\Controllers\NotaUnidadDidacticaController::class,
        'store',
    ])->middleware('permission:permissions-all|permissions-create');

    Route::patch('nota_unidad_didactica/{notaUnidadId}', [
        \App\Http\Controllers\NotaUnidadDidacticaController::class,
        'update',
    ])->middleware('permission:permissions-all|permissions-edit');

    Route::delete('nota_unidad_didactica/{notaUnidadId}', [
        \App\Http\Controllers\NotaUnidadDidacticaController::class,
        'destroy',
    ])->middleware('permission:permissions-all|permissions-delete');


    // RUTAS PARA NOTAS DE EXPERIENCIAS FORMATIVAS

    Route::get('nota_experiencia_formativa', [
        \App\Http\Controllers\NotaExperienciaFormativaController::class,
        'index',
    ])->middleware('permission:permissions-all|permissions-view');

    Route::post('nota_experiencia_formativa', [
        \App\Http\Controllers\NotaExperienciaFormativaController::class,
        'store',
    ])->middleware('permission:permissions-all|permissions-create');

    Route::patch('nota_experiencia_formativa/{notaExperienciaId}', [
        \App\Http\Controllers\NotaExperienciaFormativaController::class,
        'update',
    ])->middleware('permission:permissions-all|permissions-edit');

    Route::delete('nota_experiencia_formativa/{notaExperienciaId}', [
        \App\Http\Controllers\NotaExperienciaFormativaController::class,
        'destroy',
    ])->middleware('permission:permissions-all|permissions-delete');


    Route::get('/users-by-role/{role_id}', [UserController::class, 'getUsersByRole']);

});





