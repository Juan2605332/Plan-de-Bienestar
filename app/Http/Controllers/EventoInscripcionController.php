<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\EventoInscripcion;
use App\Models\FuncionarioPerfil;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class EventoInscripcionController extends Controller
{
    public function index(): View
    {
        $funcionario = FuncionarioPerfil::with('familiares', 'inscripciones')->findOrFail(session('funcionario_id'));
        $esPadreMadre = $funcionario->es_padre_madre
            || $funcionario->familiares->whereIn('parentesco', ['HIJO', 'HIJASTRO'])->isNotEmpty();

        // Filtrar eventos aptos según género y estado de paternidad/maternidad
        $eventos = Evento::where('estado', 'PROGRAMADO')
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

        if (
            $evento->estado !== 'PROGRAMADO'
            || ! $evento->periodo()->where('activo', true)
                ->where('fecha_inicio', '<=', now())
                ->where('fecha_cierre', '>=', now())
                ->exists()
            || ($evento->dirigido_a_genero !== 'TODOS' && $evento->dirigido_a_genero !== $funcionario->genero)
            || ($evento->requiere_ser_padre_madre && ! $esPadreMadre)
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
