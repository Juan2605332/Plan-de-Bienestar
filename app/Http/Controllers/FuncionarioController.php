<?php

namespace App\Http\Controllers;

use App\Models\CentroFormacion;
use App\Models\FuncionarioPerfil;
use App\Models\TipoCargo;
use App\Models\TipoVinculacion;
use Illuminate\Http\Request;

class FuncionarioController extends Controller
{
    public function formulario()
    {
        $funcionario = FuncionarioPerfil::with('familiares')->findOrFail(session('funcionario_id'));
        $centros = CentroFormacion::all();
        $cargos = TipoCargo::all();
        $vinculaciones = TipoVinculacion::all();

        return view('funcionario.formulario', compact('funcionario', 'centros', 'cargos', 'vinculaciones'));
    }

    public function guardar(Request $request)
    {
        $funcionario = FuncionarioPerfil::findOrFail(session('funcionario_id'));

        $data = $request->validate([
            'telefono' => 'required|string|max:25',
            'direccion_residencia' => 'required|string|max:255',
            'eps' => 'required|string|max:100',
            'fondo_pension' => 'required|string|max:100',
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

        $funcionario->update(collect($data)->except('familiares')->all());
        $funcionario->familiares()->delete();

        foreach ($data['familiares'] ?? [] as $familiar) {
            $funcionario->familiares()->create($familiar + ['es_a_cargo' => (bool) ($familiar['es_a_cargo'] ?? false)]);
        }

        $funcionario->update(['es_padre_madre' => collect($data['familiares'] ?? [])->contains(fn (array $familiar): bool => in_array($familiar['parentesco'], ['HIJO', 'HIJASTRO'], true))]);

        return redirect()->route('eventos.index')->with('success', '¡Datos y tallas actualizados correctamente!');
    }
}
