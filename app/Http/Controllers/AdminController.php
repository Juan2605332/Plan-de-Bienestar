<?php

namespace App\Http\Controllers;

use App\Exports\InscritosEventoExport;
use App\Imports\FuncionariosImport;
use App\Models\Encuesta;
use App\Models\Evento;
use App\Models\EventoInscripcion;
use App\Models\PeriodoInscripcion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class AdminController extends Controller
{
    public function dashboard(): View
    {
        $eventos = Evento::withCount('inscripciones')->orderBy('fecha_evento')->get();

        return view('admin.dashboard', compact('eventos'));
    }

    public function crearPeriodo(): View
    {
        return view('admin.periodo-formulario');
    }

    public function guardarPeriodo(Request $request): RedirectResponse
    {
        PeriodoInscripcion::create($request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'anio' => ['required', 'integer', 'min:2020', 'max:2100'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_cierre' => ['required', 'date', 'after:fecha_inicio'],
        ]));

        return redirect()->route('admin.eventos.crear')->with('success', 'Período creado. Ahora puedes crear un evento.');
    }

    public function crearEvento(): View
    {
        $periodos = PeriodoInscripcion::orderByDesc('anio')->get();

        return view('admin.evento-formulario', compact('periodos'));
    }

    public function guardarEvento(Request $request): RedirectResponse
    {
        Evento::create($request->validate([
            'periodo_id' => ['required', 'exists:periodos_inscripcion,id'],
            'nombre' => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string'],
            'fecha_evento' => ['required', 'date'],
            'lugar' => ['nullable', 'string', 'max:255'],
            'cupo_maximo' => ['nullable', 'integer', 'min:1'],
            'dirigido_a_genero' => ['required', 'in:TODOS,MASCULINO,FEMENINO'],
            'requiere_ser_padre_madre' => ['nullable', 'boolean'],
        ]) + ['requiere_ser_padre_madre' => $request->boolean('requiere_ser_padre_madre'), 'estado' => 'PROGRAMADO']);

        return redirect()->route('admin.dashboard')->with('success', 'Evento creado correctamente.');
    }

    public function crearEncuesta(Evento $evento): View
    {
        return view('admin.encuesta-formulario', compact('evento'));
    }

    public function guardarEncuesta(Request $request, Evento $evento): RedirectResponse
    {
        $encuesta = Encuesta::create($request->validate([
            'titulo' => ['required', 'string', 'max:200'],
            'instrucciones' => ['nullable', 'string'],
            'fecha_limite_respuesta' => ['nullable', 'date'],
        ]) + ['evento_id' => $evento->id, 'activa' => true]);

        foreach ($request->input('preguntas', []) as $indice => $pregunta) {
            if (blank($pregunta['enunciado'] ?? null)) {
                continue;
            }

            $nuevaPregunta = $encuesta->preguntas()->create([
                'enunciado' => $pregunta['enunciado'],
                'tipo_pregunta' => $pregunta['tipo_pregunta'] ?? 'ABIERTA',
                'orden' => $indice + 1,
                'es_obligatoria' => true,
            ]);

            foreach (array_filter(explode("\n", (string) ($pregunta['opciones'] ?? ''))) as $opcion) {
                $nuevaPregunta->opciones()->create(['texto_opcion' => trim($opcion)]);
            }
        }

        return redirect()->route('admin.dashboard')->with('success', 'Encuesta creada correctamente.');
    }

    public function inscritos(Evento $evento): View
    {
        $inscripciones = EventoInscripcion::with('funcionario')->where('evento_id', $evento->id)->latest('fecha_inscripcion')->get();

        return view('admin.inscritos', compact('evento', 'inscripciones'));
    }

    public function exportarInscritos(Evento $evento)
    {
        return Excel::download(new InscritosEventoExport($evento), "inscritos-evento-{$evento->id}.xlsx");
    }

    public function formularioImportar(): View
    {
        return view('admin.importar-funcionarios');
    }

    public function importarFuncionarios(Request $request): RedirectResponse
    {
        $data = $request->validate(['archivo' => ['required', 'file', 'mimes:xlsx,csv', 'max:5120']]);
        Excel::import(new FuncionariosImport(), $data['archivo']);

        return redirect()->route('admin.dashboard')->with('success', 'Funcionarios importados correctamente.');
    }
}
