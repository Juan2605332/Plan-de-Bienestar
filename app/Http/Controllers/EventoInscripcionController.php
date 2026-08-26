<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\EventoInscripcion;
use App\Models\FuncionarioPerfil;
use Illuminate\Http\Request;

class EventoInscripcionController extends Controller
{
    public function index()
    {
        $funcionario = FuncionarioPerfil::with('familiares', 'inscripciones')->findOrFail(session('funcionario_id'));
        $esPadreMadre = $funcionario->familiares->whereIn('parentesco', ['HIJO', 'HIJASTRO'])->count() > 0;

        // Filtrar eventos aptos según género y estado de paternidad/maternidad
        $eventos = Evento::where('estado', 'PROGRAMADO')
            ->where(function ($query) use ($funcionario) {
                $query->where('dirigido_a_genero', 'TODOS')
                      ->orWhere('dirigido_a_genero', $funcionario->genero);
            })
            ->when(!$esPadreMadre, function ($query) {
                $query->where('requiere_ser_padre_madre', false);
            })
            ->with(['encuestas' => fn ($query) => $query->where('activa', true)])
            ->get();

        $misInscripcionesIds = $funcionario->inscripciones->pluck('evento_id')->toArray();

        return view('eventos.index', compact('funcionario', 'eventos', 'misInscripcionesIds'));
    }

    public function inscribir(Evento $evento)
    {
        $funcionarioId = session('funcionario_id');

        $inscripcion = EventoInscripcion::firstOrCreate([
            'evento_id' => $evento->id,
            'funcionario_id' => $funcionarioId,
        ]);

        return back()->with('success', 'Te has inscrito exitosamente en: ' . $evento->nombre);
    }
}
