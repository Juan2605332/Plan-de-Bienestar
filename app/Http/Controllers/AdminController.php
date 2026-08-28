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
        $eventos = Evento::withCount('inscripciones')
            ->when($request->filled('buscar_evento'), function ($query) use ($request) {
                $termino = $request->string('buscar_evento')->trim();
                $query->where(function ($searchQuery) use ($termino) {
                    $searchQuery->where('nombre', 'like', "%{$termino}%")
                        ->orWhere('descripcion', 'like', "%{$termino}%")
                        ->orWhere('lugar', 'like', "%{$termino}%");
                });
            })
            ->orderBy('fecha_evento')->get();
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
            'edad_minima' => ['nullable', 'integer', 'min:0', 'max:120', 'lte:edad_maxima'],
            'edad_maxima' => ['nullable', 'integer', 'min:0', 'max:120', 'gte:edad_minima'],
            'requiere_familiar_a_cargo' => ['nullable', 'boolean'],
        ]) + ['requiere_ser_padre_madre' => $request->boolean('requiere_ser_padre_madre'), 'requiere_familiar_a_cargo' => $request->boolean('requiere_familiar_a_cargo'), 'estado' => 'PROGRAMADO']);

        return redirect()->route('admin.dashboard')->with('success', 'Evento creado correctamente.');
    }

    public function crearEncuesta(Evento $evento): View
    {
        return view('admin.encuesta-formulario', compact('evento'));
    }

    public function guardarEncuesta(Request $request, Evento $evento): RedirectResponse
    {
        $data = $request->validate([
            'titulo' => ['required', 'string', 'max:200'],
            'instrucciones' => ['nullable', 'string'],
            'fecha_limite_respuesta' => ['nullable', 'date'],
            'preguntas' => ['required', 'array', 'min:1'],
            'preguntas.*.enunciado' => ['required', 'string', 'max:1000'],
            'preguntas.*.tipo_pregunta' => ['required', 'string', 'in:ABIERTA,MULTIPLE_UNICA,MULTIPLE,ESCALA_1_5,BOOLEANO,NUMERO,FECHA,GENERO,RANGO_EDAD,HIJOS'],
            'preguntas.*.opciones' => ['nullable', 'string', 'max:5000'],
            'preguntas.*.es_obligatoria' => ['nullable', 'boolean'],
        ]);

        $encuesta = Encuesta::create(collect($data)->except('preguntas')->all() + ['evento_id' => $evento->id, 'activa' => true]);

        foreach ($data['preguntas'] as $indice => $pregunta) {
            $nuevaPregunta = $encuesta->preguntas()->create([
                'enunciado' => $pregunta['enunciado'],
                'tipo_pregunta' => $pregunta['tipo_pregunta'],
                'orden' => $indice + 1,
                'es_obligatoria' => (bool) ($pregunta['es_obligatoria'] ?? false),
            ]);

            $opciones = array_values(array_filter(array_map('trim', explode("\n", (string) ($pregunta['opciones'] ?? '')))));
            $opciones = $opciones ?: match ($pregunta['tipo_pregunta']) {
                'ESCALA_1_5' => ['1', '2', '3', '4', '5'],
                'BOOLEANO' => ['Sí', 'No'],
                'GENERO' => ['Mujer', 'Hombre', 'Otro'],
                'RANGO_EDAD' => ['18 a 25 años', '26 a 35 años', '36 a 50 años', 'Más de 50 años'],
                'HIJOS' => ['Sí', 'No'],
                default => [],
            };

            foreach ($opciones as $opcion) {
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
