<?php

namespace App\Http\Controllers;

use App\Models\Encuesta;
use App\Models\EncuestaOpcion;
use App\Models\EncuestaRespuesta;
use App\Models\EventoInscripcion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class EncuestaController extends Controller
{
    public function mostrar(Request $request, Encuesta $encuesta): View
    {
        $encuesta = $this->encuestaDisponible($request, $encuesta);
        $funcionarioId = $request->session()->get('funcionario_id');
        $respuestas = EncuestaRespuesta::where('funcionario_id', $funcionarioId)->whereIn('pregunta_id', $encuesta->preguntas->pluck('id'))->pluck('opcion_id', 'pregunta_id');

        return view('encuestas.responder', compact('encuesta', 'respuestas'));
    }

    public function responder(Request $request, Encuesta $encuesta)
    {
        $encuesta = $this->encuestaDisponible($request, $encuesta);
        $respuestas = $request->validate(['respuestas' => ['array']])['respuestas'] ?? [];
        $funcionarioId = $request->session()->get('funcionario_id');

        DB::transaction(function () use ($encuesta, $respuestas, $funcionarioId): void {
            foreach ($encuesta->preguntas as $pregunta) {
                $respuesta = $respuestas[$pregunta->id] ?? null;

                if ($pregunta->es_obligatoria && blank($respuesta)) {
                    abort(422, 'Responde todas las preguntas obligatorias.');
                }

                if (blank($respuesta)) {
                    continue;
                }

                $atributos = ['pregunta_id' => $pregunta->id, 'funcionario_id' => $funcionarioId];
                $valores = ['opcion_id' => null, 'respuesta_texto' => null, 'respuesta_numero' => null];

                if ($pregunta->tipo_pregunta === 'ABIERTA') {
                    $valores['respuesta_texto'] = (string) $respuesta;
                } else {
                    $opcion = EncuestaOpcion::where('pregunta_id', $pregunta->id)->findOrFail($respuesta);
                    $valores['opcion_id'] = $opcion->id;
                    $valores['respuesta_numero'] = $opcion->valor_numerico;
                }

                EncuestaRespuesta::updateOrCreate($atributos, $valores);
            }
        });

        return redirect()->route('eventos.index')->with('success', 'Tus respuestas fueron guardadas correctamente.');
    }

    private function encuestaDisponible(Request $request, Encuesta $encuesta): Encuesta
    {
        $encuesta->load(['evento', 'preguntas.opciones']);
        $funcionarioId = $request->session()->get('funcionario_id');

        abort_unless($encuesta->activa && ($encuesta->fecha_limite_respuesta === null || $encuesta->fecha_limite_respuesta->isFuture()), 404);
        abort_unless(EventoInscripcion::where('evento_id', $encuesta->evento_id)->where('funcionario_id', $funcionarioId)->exists(), 403);

        return $encuesta;
    }
}
