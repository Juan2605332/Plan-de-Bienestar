<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\EventoInscripcion;
use App\Models\FuncionarioPerfil;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class EventoInscripcionController extends Controller
{
    public function index(Request $request): View
    {
        $funcionario = FuncionarioPerfil::with('familiares', 'inscripciones')->findOrFail(session('funcionario_id'));
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

        $misInscripcionesIds = $funcionario->inscripciones->pluck('evento_id')->toArray();

        return view('eventos.index', compact('funcionario', 'eventos', 'misInscripcionesIds'));
    }

    public function inscribir(Evento $evento): RedirectResponse
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

        $resultado = DB::transaction(function () use ($evento, $funcionarioId): string {
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
                EventoInscripcion::create([
                    'evento_id' => $evento->id,
                    'funcionario_id' => $funcionarioId,
                ]);
            } else {
                $inscripcion->update(['estado' => 'INSCRITO', 'fecha_inscripcion' => now()]);
            }

            return 'success';
        });

        if ($resultado !== 'success') {
            return back()->with('mensaje', $resultado);
        }

        return back()->with('success', 'Te has inscrito exitosamente en: '.$evento->nombre);
    }
}
