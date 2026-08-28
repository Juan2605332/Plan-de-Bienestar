<?php

namespace App\Http\Controllers;

use App\Exports\InscritosEventoExport;
use App\Imports\FuncionariosImport;
use App\Models\Encuesta;
use App\Models\Evento;
use App\Models\EventoInscripcion;
use App\Models\FuncionarioPerfil;
use App\Models\PeriodoInscripcion;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class AdminController extends Controller
{
    public function dashboard(Request $request): View
    {
        $eventos = Evento::withCount('inscripciones')->orderBy('fecha_evento')->get();
        $funcionarios = FuncionarioPerfil::query()->withCount(['familiares', 'familiares as familiares_a_cargo_count' => fn ($query) => $query->where('es_a_cargo', true)])->where('activo', true)
            ->when($request->filled('genero'), fn ($query) => $query->where('genero', $request->string('genero')))
            ->when($request->filled('edad_min'), fn ($query) => $query->whereDate('fecha_nacimiento', '<=', now()->subYears((int) $request->integer('edad_min'))))
            ->when($request->filled('edad_max'), fn ($query) => $query->whereDate('fecha_nacimiento', '>=', now()->subYears((int) $request->integer('edad_max') + 1)))
            ->when($request->boolean('padres'), fn ($query) => $query->where('es_padre_madre', true))
            ->when($request->boolean('a_cargo'), fn ($query) => $query->whereHas('familiares', fn ($familiarQuery) => $familiarQuery->where('es_a_cargo', true)))
            ->when($request->boolean('cumpleanos'), fn ($query) => $query->whereMonth('fecha_nacimiento', now()->month))
            ->orderBy('apellidos')->orderBy('nombres')->get();

        return view('admin.dashboard', compact('eventos', 'funcionarios'));
    }

    public function calendario(Request $request): View
    {
        $mes = min(12, max(1, (int) $request->integer('mes', now('America/Bogota')->month)));
        $anio = min(2100, max(2020, (int) $request->integer('anio', now('America/Bogota')->year)));
        $inicio = CarbonImmutable::create($anio, $mes, 1)->startOfMonth();
        $fin = $inicio->endOfMonth();
        $eventos = Evento::query()->whereBetween('fecha_evento', [$inicio->toDateString(), $fin->toDateString()])->orderBy('fecha_evento')->get()->groupBy(fn (Evento $evento): string => $evento->fecha_evento->toDateString());

        return view('admin.calendario', compact('eventos', 'inicio', 'fin', 'mes', 'anio'));
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
        Excel::import(new FuncionariosImport, $data['archivo']);

        return redirect()->route('admin.dashboard')->with('success', 'Funcionarios importados correctamente.');
    }
}
