<?php

namespace App\Http\Controllers;

use App\Models\CentroFormacion;
use App\Models\Evento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminReporteController extends Controller
{
    public function index(Request $request): View
    {
        $anio = $this->anio($request);

        return view('admin.reportes', [
            'anio' => $anio,
            'filas' => $this->filas($anio),
        ]);
    }

    public function exportar(Request $request): StreamedResponse
    {
        $anio = $this->anio($request);
        $filas = $this->filas($anio);

        return response()->streamDownload(function () use ($filas): void {
            $salida = fopen('php://output', 'w');
            fwrite($salida, "\xEF\xBB\xBF");
            fputcsv($salida, ['Evento', 'Fecha', 'Centro de formación', 'Funcionarios activos', 'Inscritos', 'Asistentes', '% funcionarios inscritos', '% asistencia sobre inscritos', 'Satisfacción promedio (1-5)']);

            foreach ($filas as $fila) {
                fputcsv($salida, [
                    $fila['evento'],
                    $fila['fecha'],
                    $fila['centro'],
                    $fila['funcionarios'],
                    $fila['inscritos'],
                    $fila['asistentes'],
                    $fila['porcentaje_inscritos'],
                    $fila['porcentaje_asistencia'],
                    $fila['satisfaccion'],
                ]);
            }

            fclose($salida);
        }, "reporte-bienestar-{$anio}.csv", ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function anio(Request $request): int
    {
        $data = $request->validate([
            'anio' => ['nullable', 'integer', 'min:2020', 'max:2100'],
        ]);

        return (int) ($data['anio'] ?? now('America/Bogota')->year);
    }

    private function filas(int $anio): array
    {
        $eventos = Evento::query()
            ->whereYear('fecha_evento', $anio)
            ->where('estado', '!=', 'CANCELADO')
            ->orderBy('fecha_evento')
            ->get(['id', 'nombre', 'fecha_evento']);
        $centros = CentroFormacion::orderBy('nombre')->get(['id', 'nombre']);

        $conteos = DB::table('evento_inscripciones')
            ->join('funcionarios_perfil', 'funcionarios_perfil.id', '=', 'evento_inscripciones.funcionario_id')
            ->leftJoin('evento_asistencias', 'evento_asistencias.inscripcion_id', '=', 'evento_inscripciones.id')
            ->select('evento_inscripciones.evento_id', 'funcionarios_perfil.centro_formacion_id')
            ->selectRaw("SUM(CASE WHEN evento_inscripciones.estado = 'INSCRITO' THEN 1 ELSE 0 END) AS inscritos")
            ->selectRaw("SUM(CASE WHEN evento_inscripciones.estado = 'INSCRITO' AND evento_asistencias.asistio = 1 THEN 1 ELSE 0 END) AS asistentes")
            ->groupBy('evento_inscripciones.evento_id', 'funcionarios_perfil.centro_formacion_id')
            ->get()
            ->keyBy(fn (object $fila): string => "{$fila->evento_id}:{$fila->centro_formacion_id}");

        $activosPorCentro = DB::table('funcionarios_perfil')
            ->where('activo', true)
            ->select('centro_formacion_id')
            ->selectRaw('COUNT(*) AS total')
            ->groupBy('centro_formacion_id')
            ->pluck('total', 'centro_formacion_id');

        $satisfaccion = DB::table('encuestas')
            ->join('encuesta_preguntas', 'encuesta_preguntas.encuesta_id', '=', 'encuestas.id')
            ->join('encuesta_respuestas', 'encuesta_respuestas.pregunta_id', '=', 'encuesta_preguntas.id')
            ->where('encuesta_preguntas.tipo_pregunta', 'ESCALA_1_5')
            ->whereNotNull('encuesta_respuestas.respuesta_numero')
            ->selectRaw('encuestas.evento_id AS evento_id')
            ->selectRaw('AVG(encuesta_respuestas.respuesta_numero) AS promedio')
            ->groupBy('encuestas.evento_id')
            ->pluck('promedio', 'evento_id');

        $filas = [];

        foreach ($eventos as $evento) {
            foreach ($centros as $centro) {
                $conteo = $conteos->get("{$evento->id}:{$centro->id}");
                $inscritos = (int) ($conteo->inscritos ?? 0);
                $asistentes = (int) ($conteo->asistentes ?? 0);
                $funcionarios = (int) ($activosPorCentro[$centro->id] ?? 0);

                $filas[] = [
                    'evento' => $evento->nombre,
                    'fecha' => $evento->fecha_evento->format('Y-m-d'),
                    'centro' => $centro->nombre,
                    'funcionarios' => $funcionarios,
                    'inscritos' => $inscritos,
                    'asistentes' => $asistentes,
                    'porcentaje_inscritos' => $funcionarios > 0 ? round($inscritos * 100 / $funcionarios, 2) : 0,
                    'porcentaje_asistencia' => $inscritos > 0 ? round($asistentes * 100 / $inscritos, 2) : 0,
                    'satisfaccion' => isset($satisfaccion[$evento->id]) ? round((float) $satisfaccion[$evento->id], 2) : null,
                ];
            }
        }

        return $filas;
    }
}
