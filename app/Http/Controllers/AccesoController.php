<?php

namespace App\Http\Controllers;

use App\Models\FuncionarioPerfil;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AccesoController extends Controller
{
    public function index(): View
    {
        return view('acceso');
    }

    public function ingresar(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'usuario' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string'],
        ]);

        $request->session()->forget('funcionario_id');

        if (! Auth::attempt([
            'email' => $data['usuario'],
            'password' => $data['password'],
            'activo' => true,
        ])) {
            return back()->withInput($request->only('usuario'))->with('mensaje', 'El usuario o la contraseña no son correctos.');
        }

        $usuario = Auth::user();

        if ($usuario->is_admin) {
            $destino = 'admin.dashboard';
        } else {
            $funcionario = FuncionarioPerfil::query()
                ->whereRaw('LOWER(email) = ?', [strtolower($usuario->email)])
                ->where('activo', true)
                ->first();

            if ($funcionario === null) {
                Auth::logout();

                return back()->withInput($request->only('usuario'))->with('mensaje', 'No hay un perfil de funcionario activo asociado a este usuario.');
            }

            $request->session()->put('funcionario_id', $funcionario->id);
            $destino = 'funcionario.formulario';
        }

        $request->session()->regenerate();

        return redirect()->route($destino);
    }

    public function salir(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('acceso');
    }
}
