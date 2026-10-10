<?php

namespace App\Http\Controllers;

use App\Models\EventoInscripcion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class EventoAsistenciaController extends Controller
{
    public function confirmar(string $token): View
    {
        $inscripcion = EventoInscripcion::with(['evento', 'funcionario', 'asistencia'])
            ->where('qr_token', $token)
            ->firstOrFail();

        return view('admin.asistencia-confirmar', compact('inscripcion'));
    }

    public function registrar(string $token): RedirectResponse
    {
        $inscripcion = DB::transaction(function () use ($token): EventoInscripcion {
            $inscripcion = EventoInscripcion::with(['evento', 'asistencia'])
                ->where('qr_token', $token)
                ->lockForUpdate()
                ->firstOrFail();

            abort_unless($inscripcion->estado === 'INSCRITO', 422, 'La inscripción no está activa.');
            abort_unless(
                $inscripcion->evento->fecha_evento->toDateString() === now('America/Bogota')->toDateString(),
                422,
                'La asistencia solo se puede registrar el día del evento.'
            );
            abort_unless($inscripcion->evento->estado !== 'CANCELADO', 422, 'El evento fue cancelado.');

            if (! $inscripcion->asistencia?->asistio) {
                $inscripcion->asistencia()->updateOrCreate([], [
                    'asistio' => true,
                    'hora_registro' => now('America/Bogota'),
                ]);
            }

            return $inscripcion;
        });

        return redirect()->route('admin.asistencia.confirmar', $inscripcion->qr_token)
            ->with('success', 'La asistencia quedó registrada correctamente.');
    }
}
