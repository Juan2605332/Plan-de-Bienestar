<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\EventoInscripcion;
use App\Models\FuncionarioPerfil;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use RuntimeException;
use Throwable;

class EventoInscripcionController extends Controller
{
    public function index(Request $request): View
    {
        $funcionario = FuncionarioPerfil::with(['familiares', 'inscripciones.asistencia', 'inscripciones.documentos'])->findOrFail(session('funcionario_id'));
        $esPadreMadre = $funcionario->es_padre_madre
            || $funcionario->familiares->whereIn('parentesco', ['HIJO', 'HIJASTRO'])->isNotEmpty();
        $tieneFamiliarACargo = $funcionario->familiares->contains('es_a_cargo', true);
        $hoy = CarbonImmutable::today();

        // Filtrar eventos aptos según género y estado de paternidad/maternidad
        $eventos = Evento::where('estado', 'PROGRAMADO')
            ->when($request->filled('buscar'), function ($query) use ($request) {
                $termino = $request->string('buscar')->trim();
                $query->where(function ($searchQuery) use ($termino) {
                    $searchQuery->where('nombre', 'like', "%{$termino}%")
                        ->orWhere('descripcion', 'like', "%{$termino}%")
                        ->orWhere('lugar', 'like', "%{$termino}%");
                });
            })
            ->whereHas('periodo', function ($query) {
                $query->where('activo', true)
                    ->where('fecha_inicio', '<=', now())
                    ->where('fecha_cierre', '>=', now());
            })
            ->where(function ($query) use ($funcionario) {
                $query->where('dirigido_a_genero', 'TODOS')
                    ->orWhere('dirigido_a_genero', $funcionario->genero);
            })
            ->when(! $esPadreMadre, function ($query) {
                $query->where('requiere_ser_padre_madre', false);
            })
            ->when(! $tieneFamiliarACargo, function ($query) {
                $query->where('requiere_familiar_a_cargo', false);
            })
            ->where(function ($query) use ($funcionario, $hoy) {
                $edad = $hoy->diffInYears($funcionario->fecha_nacimiento);

                $query->where(function ($minQuery) use ($edad) {
                    $minQuery->whereNull('edad_minima')->orWhere('edad_minima', '<=', $edad);
                })->where(function ($maxQuery) use ($edad) {
                    $maxQuery->whereNull('edad_maxima')->orWhere('edad_maxima', '>=', $edad);
                });
            })
            ->with(['encuestas' => fn ($query) => $query->where('activa', true)])
            ->get();

        $inscripcionesActivas = $funcionario->inscripciones->where('estado', 'INSCRITO');
        $misInscripcionesIds = $inscripcionesActivas->pluck('evento_id')->all();
        $inscripcionesPorEvento = $inscripcionesActivas->keyBy('evento_id');

        return view('eventos.index', compact('funcionario', 'eventos', 'misInscripcionesIds', 'inscripcionesPorEvento'));
    }

    public function calendario(): View
    {
        $inicio = CarbonImmutable::create(now('America/Bogota')->year, 1, 1)->startOfDay();
        $fin = $inicio->endOfYear();
        $eventos = Evento::query()
            ->whereBetween('fecha_evento', [$inicio->toDateString(), $fin->toDateString()])
            ->whereIn('estado', ['PROGRAMADO', 'EN_CURSO', 'FINALIZADO'])
            ->orderBy('fecha_evento')
            ->get()
            ->groupBy(fn (Evento $evento): string => $evento->fecha_evento->locale('es')->translatedFormat('F'));

        return view('eventos.calendario', compact('eventos'));
    }

    public function inscribir(Request $request, Evento $evento): RedirectResponse
    {
        $funcionarioId = session('funcionario_id');
        $funcionario = FuncionarioPerfil::with('familiares')->findOrFail($funcionarioId);
        $esPadreMadre = $funcionario->es_padre_madre
            || $funcionario->familiares->whereIn('parentesco', ['HIJO', 'HIJASTRO'])->isNotEmpty();
        $tieneFamiliarACargo = $funcionario->familiares->contains('es_a_cargo', true);
        $edad = CarbonImmutable::today()->diffInYears($funcionario->fecha_nacimiento);

        if (
            $evento->estado !== 'PROGRAMADO'
            || ! $evento->periodo()->where('activo', true)
                ->where('fecha_inicio', '<=', now())
                ->where('fecha_cierre', '>=', now())
                ->exists()
            || ($evento->dirigido_a_genero !== 'TODOS' && $evento->dirigido_a_genero !== $funcionario->genero)
            || ($evento->requiere_ser_padre_madre && ! $esPadreMadre)
            || ($evento->requiere_familiar_a_cargo && ! $tieneFamiliarACargo)
            || ($evento->edad_minima !== null && $edad < $evento->edad_minima)
            || ($evento->edad_maxima !== null && $edad > $evento->edad_maxima)
        ) {
            return back()->with('mensaje', 'No cumples las condiciones para inscribirte en este evento.');
        }

        $yaInscrito = EventoInscripcion::query()
            ->where('evento_id', $evento->id)
            ->where('funcionario_id', $funcionarioId)
            ->where('estado', 'INSCRITO')
            ->exists();

        if ($yaInscrito) {
            return back()->with('mensaje', 'Ya estás inscrito en este evento.');
        }

        $requisitos = $evento->documentos_requeridos ?? [];
        $uploads = $request->validate([
            'documentos' => ['nullable', 'array', 'max:'.count($requisitos)],
            'documentos.*' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ])['documentos'] ?? [];

        foreach ($requisitos as $indice => $requisito) {
            if (! isset($uploads[$indice])) {
                return back()->withErrors(["documentos.{$indice}" => "Adjunta el documento requerido: {$requisito}."])->withInput();
            }
        }

        $resultado = DB::transaction(function () use ($evento, $funcionarioId): EventoInscripcion|string {
            $evento = Evento::query()->lockForUpdate()->findOrFail($evento->id);
            $inscripcion = EventoInscripcion::query()
                ->where('evento_id', $evento->id)
                ->where('funcionario_id', $funcionarioId)
                ->first();

            if ($inscripcion?->estado === 'INSCRITO') {
                return 'Ya estás inscrito en este evento.';
            }

            $inscritos = $evento->inscripciones()->where('estado', 'INSCRITO')->count();
            if ($evento->cupo_maximo !== null && $inscritos >= $evento->cupo_maximo) {
                return 'El evento ya alcanzó su cupo máximo.';
            }

            if ($inscripcion === null) {
                return EventoInscripcion::create([
                    'evento_id' => $evento->id,
                    'funcionario_id' => $funcionarioId,
                    'qr_token' => (string) Str::uuid(),
                ]);
            }

            $inscripcion->update([
                'estado' => 'INSCRITO',
                'fecha_inscripcion' => now(),
                'qr_token' => $inscripcion->qr_token ?? (string) Str::uuid(),
            ]);

            return $inscripcion;
        });

        if (is_string($resultado)) {
            return back()->with('mensaje', $resultado);
        }

        $rutasGuardadas = [];
        $rutasAnteriores = [];

        try {
            DB::transaction(function () use ($resultado, $requisitos, $uploads, $evento, &$rutasGuardadas, &$rutasAnteriores): void {
                foreach ($requisitos as $indice => $requisito) {
                    $archivo = $uploads[$indice];
                    $ruta = $archivo->store("eventos/{$evento->id}/documentos", 'local');
                    if ($ruta === false) {
                        throw new RuntimeException('No fue posible guardar el documento de inscripción.');
                    }

                    $rutasGuardadas[] = $ruta;
                    $documentoExistente = $resultado->documentos()->where('tipo_documento', $requisito)->first();
                    if ($documentoExistente !== null) {
                        $rutasAnteriores[] = $documentoExistente->ruta;
                    }

                    $resultado->documentos()->updateOrCreate(
                        ['tipo_documento' => $requisito],
                        [
                            'ruta' => $ruta,
                            'nombre_original' => $archivo->getClientOriginalName(),
                            'tamano_bytes' => $archivo->getSize(),
                        ]
                    );
                }
            });
        } catch (Throwable $exception) {
            foreach ($rutasGuardadas as $ruta) {
                if (! Storage::disk('local')->delete($ruta)) {
                    Log::error('No fue posible limpiar un documento de inscripción tras un error.', ['ruta' => $ruta]);
                }
            }

            throw $exception;
        }

        foreach (array_diff($rutasAnteriores, $rutasGuardadas) as $rutaAnterior) {
            if (! Storage::disk('local')->delete($rutaAnterior)) {
                throw new RuntimeException('La inscripción se guardó, pero no fue posible eliminar el archivo sustituido.');
            }
        }

        return back()->with('success', 'Te has inscrito exitosamente en: '.$evento->nombre);
    }
}
