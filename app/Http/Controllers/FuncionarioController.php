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
        ]);

        $funcionario->update($data);

        return redirect()->route('eventos.index')->with('success', '¡Datos y tallas actualizados correctamente!');
    }
}