<?php

namespace App\Http\Controllers;

use App\Models\FuncionarioPerfil;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccesoController extends Controller
{
    public function index(): View
    {
        return view('acceso');
    }

    public function ingresar(Request $request): RedirectResponse
    {
        $data = $request->validate(['cedula' => ['required', 'string', 'max:20']]);
        $funcionario = FuncionarioPerfil::query()->where('cedula', $data['cedula'])->where('activo', true)->first();

        if ($funcionario === null) {
            return back()->withInput()->with('mensaje', 'No encontramos un funcionario activo con ese número de documento.');
        }

        $request->session()->regenerate();
        $request->session()->put('funcionario_id', $funcionario->id);

        return redirect()->route('funcionario.formulario');
    }

    public function salir(Request $request): RedirectResponse
    {
        $request->session()->forget('funcionario_id');
        $request->session()->regenerateToken();

        return redirect()->route('acceso');
    }
}
