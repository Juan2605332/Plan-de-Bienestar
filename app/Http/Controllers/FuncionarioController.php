<?php

namespace App\Http\Controllers;

use App\Models\CentroFormacion;
use App\Models\FuncionarioEdicionAutorizada;
use App\Models\FuncionarioPerfil;
use App\Models\PeriodoActualizacionDatos;
use App\Models\TipoCargo;
use App\Models\TipoVinculacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        return view('funcionario.formulario', compact('funcionario', 'centros', 'cargos', 'vinculaciones', 'periodo', 'autorizacion', 'puedeActualizar'));
    }

    public function guardar(Request $request)
    {
        $funcionario = FuncionarioPerfil::findOrFail(session('funcionario_id'));
        $periodoAbierto = PeriodoActualizacionDatos::where('activo', true)
            ->where('fecha_inicio', '<=', now())
            ->where('fecha_cierre', '>=', now())
            ->exists();
        $autorizacion = $this->autorizacionVigente($funcionario->id);

        abort_unless($periodoAbierto || $autorizacion !== null, 403, 'El período de actualización está cerrado.');

        $data = $request->validate([
            'correo_personal' => ['nullable', 'email', 'max:150'],
            'correo_institucional' => ['nullable', 'email', 'max:150'],
            'telefono' => 'required|string|max:25',
            'direccion_residencia' => 'required|string|max:255',
            'eps' => 'required|string|max:100',
            'fondo_pension' => 'required|string|max:100',
            'nivel_academico' => ['nullable', 'string', 'max:100'],
            'titulo_academico' => ['nullable', 'string', 'max:150'],
            'area' => ['nullable', 'string', 'max:150'],
            'red_conocimiento' => ['nullable', 'string', 'max:150'],
            'talla_camisa' => 'required|string|max:10',
            'talla_pantalon' => 'required|string|max:10',
            'talla_calzado' => 'required|string|max:10',
            'familiares' => ['nullable', 'array'],
            'familiares.*.parentesco' => ['required', 'in:HIJO,HIJASTRO,CONYUGE,OTRO'],
            'familiares.*.nombres' => ['required', 'string', 'max:100'],
            'familiares.*.apellidos' => ['required', 'string', 'max:100'],
            'familiares.*.tipo_documento' => ['required', 'string', 'max:10'],
            'familiares.*.numero_documento' => ['nullable', 'string', 'max:20'],
            'familiares.*.fecha_nacimiento' => ['required', 'date', 'before_or_equal:today'],
            'familiares.*.genero' => ['required', 'in:MASCULINO,FEMENINO,OTRO'],
            'familiares.*.es_a_cargo' => ['nullable', 'boolean'],
        ]);

        if (array_key_exists('correo_institucional', $data)) {
            $data['email'] = $data['correo_institucional'];
        }

        DB::transaction(function () use ($funcionario, $data, $periodoAbierto, $autorizacion): void {
            if ($autorizacion !== null) {
                $autorizacion = FuncionarioEdicionAutorizada::query()->lockForUpdate()->find($autorizacion->id);
                abort_unless($autorizacion !== null && $autorizacion->usado_en === null && $autorizacion->expira_en->isFuture(), 403, 'La autorización de edición ya no está vigente.');
            }

            $funcionario->update(collect($data)->except('familiares')->all());
            $funcionario->familiares()->delete();

            foreach ($data['familiares'] ?? [] as $familiar) {
                $funcionario->familiares()->create($familiar + ['es_a_cargo' => (bool) ($familiar['es_a_cargo'] ?? false)]);
            }

            $funcionario->update(['es_padre_madre' => collect($data['familiares'] ?? [])->contains(fn (array $familiar): bool => in_array($familiar['parentesco'], ['HIJO', 'HIJASTRO'], true))]);

            if (! $periodoAbierto && $autorizacion !== null) {
                $autorizacion->update(['usado_en' => now()]);
            }
        });

        return redirect()->route('funcionario.formulario')->with('success', 'Tus datos fueron actualizados correctamente.');
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
