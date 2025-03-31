<?php

namespace App\Http\Controllers;

use App\Models\Programa;
use App\Models\UnidadDidactica;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UnidadDidacticaController extends Controller
{
    public function index()
    {
        // Incluye la relación con el programa asociado
        $unidades = UnidadDidactica::with('programa')->get();
        return response()->json($unidades, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validación
        $validator = Validator::make($request->all(), [
            'nombre_unidad' => 'required|string|max:255',
            'fecha_inicio' => 'required',
            'fecha_fin' => 'required',
            'creditos' => 'required|integer',
            'dias' => 'required|integer',
            'horas' => 'required|integer',
            'capacidad' => 'required',
            'numero_unidad' => 'required',
            'id_programa' => 'required|exists:programas,id_programa',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors(),
                'status' => 400,
            ], 400);
        }

        // Crear la unidad didáctica
        $unidad = UnidadDidactica::create($request->all());

        return response()->json([
            'message' => 'Unidad didáctica creada exitosamente',
            'unidad' => $unidad,
            'status' => 201,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Incluye la relación con el programa asociado
        $unidad = UnidadDidactica::with('programa')->find($id);

        if (!$unidad) {
            return response()->json([
                'message' => 'Unidad didáctica no encontrada',
                'status' => 404,
            ], 404);
        }

        return response()->json($unidad, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $unidad = UnidadDidactica::find($id);

        if (!$unidad) {
            return response()->json([
                'message' => 'Unidad didáctica no encontrada',
                'status' => 404,
            ], 404);
        }

        // Validación
        $validator = Validator::make($request->all(), [
            'nombre_unidad' => 'string|max:255',
            'fecha_inicio' => 'required',
            'fecha_fin' => 'required',
            'creditos' => 'required|integer',
            'dias' => 'required|integer',
            'horas' => 'required|integer',
            'numero_unidad' => 'required',
            'id_programa' => 'exists:programas,id_programa',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors(),
                'status' => 400,
            ], 400);
        }

        // Actualizar la unidad didáctica
        $unidad->update($request->all());

        return response()->json([
            'message' => 'Unidad didáctica actualizada exitosamente',
            'unidad' => $unidad,
            'status' => 200,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $unidad = UnidadDidactica::find($id);

        if (!$unidad) {
            return response()->json([
                'message' => 'Unidad didáctica no encontrada',
                'status' => 404,
            ], 404);
        }

        // Eliminar la unidad didáctica
        $unidad->delete();

        return response()->json([
            'message' => 'Unidad didáctica eliminada exitosamente',
            'status' => 204,
        ], 204);
    }

    public function getUnidadDidacticaPrograma($id_programa)
    {
        $programa = Programa::with('unidadesDidacticas')
            ->where('id_programa', $id_programa)
            ->first();

        if (!$programa) {
            return response()->json(['message' => 'Programa no encontrado'], 404);
        }

        return response()->json([
            'programa' => $programa->only(['id_programa', 'nombre_programa']),
            'unidades_didacticas' => $programa->unidadesDidacticas
                ->sortBy('numero_unidad') // Ordenar por numero_unidad
                ->values() // Reindexar la colección después de ordenar
                ->map(function ($unidad) {
                    return [
                        'id_unidad_didactica' => $unidad->id_unidad_didactica,
                        'nombre_unidad' => $unidad->nombre_unidad,
                        'fecha_inicio' => $unidad->fecha_inicio,
                        'fecha_fin' => $unidad->fecha_fin,
                        'creditos' => $unidad->creditos,
                        'dias' => $unidad->dias,
                        'horas' => $unidad->horas,
                        'capacidad' => $unidad->capacidad,
                        'numero_unidad' => $unidad->numero_unidad
                    ];
                })
        ], 200);
    }
    public function getUnidadesByPrograma($id_programa)
    {
        $programa = Programa::find($id_programa);

        if (!$programa) {
            return response()->json([
                'message' => 'Programa no encontrado',
                'status' => 404
            ], 404);
        }

        $unidades = UnidadDidactica::where('id_programa', $id_programa)
            ->select('nombre_unidad', 'numero_unidad')
            ->get();

        return response()->json([
            'unidades_didacticas' => $unidades,
            'status' => 200
        ], 200);
    }
}
