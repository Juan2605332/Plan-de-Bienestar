<?php

namespace App\Http\Controllers;

use App\Models\FuncionarioEdicionAutorizada;
use App\Models\FuncionarioPerfil;
use App\Models\PeriodoActualizacionDatos;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminDatosController extends Controller
{
    public function index(): View
    {
        $periodo = PeriodoActualizacionDatos::latest('fecha_inicio')->first();
        $autorizaciones = FuncionarioEdicionAutorizada::with('funcionario')
            ->latest()
            ->limit(30)
            ->get();

        return view('admin.datos-configuracion', compact('periodo', 'autorizaciones'));
    }

    public function guardarPeriodo(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_cierre' => ['required', 'date', 'after:fecha_inicio'],
        ]);

        DB::transaction(function () use ($data): void {
            PeriodoActualizacionDatos::where('activo', true)->update(['activo' => false]);
            PeriodoActualizacionDatos::create($data + [
                'activo' => true,
                'administrador_id' => auth()->id(),
            ]);
        });

        return back()->with('success', 'El período de actualización de datos quedó configurado.');
    }

    public function autorizarEdicion(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'cedula' => ['required', 'string', 'max:20', 'exists:funcionarios_perfil,cedula'],
            'motivo' => ['required', 'string', 'max:1000'],
            'expira_en' => ['required', 'date', 'after:now'],
        ]);
        $funcionario = FuncionarioPerfil::where('cedula', $data['cedula'])->firstOrFail();

        FuncionarioEdicionAutorizada::create([
            'funcionario_id' => $funcionario->id,
            'administrador_id' => auth()->id(),
            'motivo' => $data['motivo'],
            'expira_en' => $data['expira_en'],
        ]);

        return back()->with('success', "Se autorizó una actualización para {$funcionario->nombres} {$funcionario->apellidos}.");
    }
}
