<?php

namespace App\Http\Controllers;

use App\Models\ExperienciaFormativa;
use App\Models\NotaExperienciaFormativa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NotaExperienciaFormativaController extends Controller
{
    public function index()
    {
        $notas = NotaExperienciaFormativa::with(['experienciaFormativa', 'estudiante', 'grupo'])->get();
        return response()->json($notas);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validación de datos
        $validator = Validator::make($request->all(), [
            'nota' => 'required|string',
            'id_experiencia' => 'required|exists:experiencias_formativas,id_experiencia',
            'id_estudiante' => 'required|exists:users,id',
            'id_grupo' => 'required|exists:grupos,id_grupo',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        // Crear una nueva nota
        $nota = NotaExperienciaFormativa::create([
            'nota' => $request->nota,
            'id_experiencia' => $request->id_experiencia,
            'id_estudiante' => $request->id_estudiante,
            'id_grupo' => $request->id_grupo,
        ]);

        return response()->json([
            'nota' => $nota,
            'status' => 201
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $nota = NotaExperienciaFormativa::with(['experienciaFormativa', 'estudiante', 'grupo'])->find($id);

        if (!$nota) {
            return response()->json([
                'message' => 'Nota no encontrada',
                'status' => 404
            ], 404);
        }

        return response()->json($nota);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validación de datos
        $validator = Validator::make($request->all(), [
            'nota' => 'required|string',
            'id_experiencia' => 'required|exists:experiencias_formativas,id_experiencia',
            'id_estudiante' => 'required|exists:users,id',
            'id_grupo' => 'required|exists:grupos,id_grupo',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        // Encontrar el registro existente
        $nota = NotaExperienciaFormativa::find($id);

        if (!$nota) {
            return response()->json([
                'message' => 'Nota no encontrada',
                'status' => 404
            ], 404);
        }

        // Actualizar el registro
        $nota->nota = $request->nota;
        $nota->id_experiencia = $request->id_experiencia;
        $nota->id_estudiante = $request->id_estudiante;
        $nota->id_grupo = $request->id_grupo;
        $nota->save();

        return response()->json([
            'nota' => $nota,
            'status' => 200
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Encontrar el registro existente
        $nota = NotaExperienciaFormativa::find($id);

        if (!$nota) {
            return response()->json([
                'message' => 'Nota no encontrada',
                'status' => 404
            ], 404);
        }

        // Eliminar el registro
        $nota->delete();

        return response()->json([
            'message' => 'Nota eliminada exitosamente',
            'status' => 204
        ], 204);
    }

    public function registrarNotaExperiencia(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'notas' => 'required|array',
            'notas.*.nota' => 'required|string',
            'notas.*.id_experiencia' => 'required|exists:experiencias_formativas,id_experiencia',
            'notas.*.id_estudiante' => 'required|exists:users,id',
            'notas.*.id_grupo' => 'required|exists:grupos,id_grupo',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        // Insertar las notas masivamente
        $notas = array_map(function ($nota) {
            return [
                'nota' => $nota['nota'],
                'id_experiencia' => $nota['id_experiencia'],
                'id_estudiante' => $nota['id_estudiante'],
                'id_grupo' => $nota['id_grupo'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }, $request->notas);

        NotaExperienciaFormativa::insert($notas);

        return response()->json([
            'message' => 'Notas guardadas exitosamente',
            'status' => 201
        ], 201);
    }
}
