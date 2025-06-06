<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use App\Models\Matricula;
use App\Models\NotaUnidadDidactica;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class NotaUnidadDidacticaController extends Controller
{
    public function index()
    {
        $notas = NotaUnidadDidactica::with(['unidadDidactica', 'estudiante', 'grupo'])->get();
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
            'id_unidad_didactica' => 'required|exists:unidades_didacticas,id_unidad_didactica',
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
        $nota = NotaUnidadDidactica::create([
            'id_nota' => Str::uuid(),
            'nota' => $request->nota,
            'id_unidad_didactica' => $request->id_unidad_didactica,
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
        $nota = NotaUnidadDidactica::with(['unidadDidactica', 'estudiante', 'grupo'])->find($id);

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
        $validator = Validator::make($request->all(), [
            'nota' => 'required|string',
            'id_unidad_didactica' => 'required|exists:unidades_didacticas,id_unidad_didactica',
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

        $notaUnidadDidactica = NotaUnidadDidactica::find($id);

        if (!$notaUnidadDidactica) {
            return response()->json([
                'message' => 'NotaUnidadDidactica no encontrada',
                'status' => 404
            ], 404);
        }

        $notaUnidadDidactica->nota = $request->nota;
        $notaUnidadDidactica->id_unidad_didactica = $request->id_unidad_didactica;
        $notaUnidadDidactica->id_estudiante = $request->id_estudiante;
        $notaUnidadDidactica->id_grupo = $request->id_grupo;
        $notaUnidadDidactica->save();

        return response()->json([
            'nota_unidad_didactica' => $notaUnidadDidactica,
            'status' => 200
        ], 200);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Encontrar el registro existente
        $nota = NotaUnidadDidactica::find($id);

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

    public function registrarNotas(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'notas' => 'required|array',
            'notas.*.nota' => 'required|string',
            'notas.*.id_unidad_didactica' => 'required|exists:unidades_didacticas,id_unidad_didactica',
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

        $notasFiltradas = [];
        $notasDuplicadas = [];

        foreach ($request->notas as $nota) {
            // Verificar si ya existe una nota para ese estudiante en la unidad didáctica
            $existeNota = NotaUnidadDidactica::where('id_estudiante', $nota['id_estudiante'])
                ->where('id_unidad_didactica', $nota['id_unidad_didactica'])
                ->exists();

            if (!$existeNota) {
                $notasFiltradas[] = [
                    'id_nota' => (string) Str::uuid(),
                    'nota' => $nota['nota'],
                    'id_unidad_didactica' => $nota['id_unidad_didactica'],
                    'id_estudiante' => $nota['id_estudiante'],
                    'id_grupo' => $nota['id_grupo'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            } else {
                $notasDuplicadas[] = $nota['id_unidad_didactica'];
            }
        }

        // Insertar solo las notas que no existen
        if (!empty($notasFiltradas)) {
            NotaUnidadDidactica::insert($notasFiltradas);
        }

        if (!empty($notasDuplicadas)) {
            return response()->json([
                'message' => 'Ya existe notas en esta unidad didactica',
                'status' => 409
            ], 409);
        }

        return response()->json([
            'message' => 'Notas guardadas exitosamente',
            'status' => 201
        ], 201);
    }
}
