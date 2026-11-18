<?php

namespace App\Http\Controllers;

use App\Models\CentroFormacion;
use App\Models\FuncionarioEdicionAutorizada;
use App\Models\FuncionarioPerfil;
use App\Models\PeriodoActualizacionDatos;
use App\Models\TipoCargo;
use App\Models\TipoVinculacion;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class FuncionarioController extends Controller
{
    public function formulario(): View
    {
        $funcionario = FuncionarioPerfil::with('familiares')->findOrFail(session('funcionario_id'));
        $centros = CentroFormacion::all();
        $cargos = TipoCargo::all();
        $vinculaciones = TipoVinculacion::all();
        $periodo = PeriodoActualizacionDatos::where('activo', true)->latest('fecha_inicio')->first();
        $autorizacion = $this->autorizacionVigente($funcionario->id);
        $puedeActualizar = ($periodo?->estaAbierto() ?? false) || $autorizacion !== null;
        $usuario = Auth::user();

        return view('funcionario.formulario', compact('funcionario', 'usuario', 'centros', 'cargos', 'vinculaciones', 'periodo', 'autorizacion', 'puedeActualizar'));
    }

    private function autorizacionVigente(int $funcionarioId): ?FuncionarioEdicionAutorizada
    {
        return FuncionarioEdicionAutorizada::query()
            ->where('funcionario_id', $funcionarioId)
            ->whereNull('usado_en')
            ->where('expira_en', '>', now())
            ->latest('id')
            ->first();
    }
}
