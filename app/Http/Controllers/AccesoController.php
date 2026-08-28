<?php

namespace App\Http\Controllers;

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

        if (! Auth::attempt([
            'email' => $data['usuario'],
            'password' => $data['password'],
            'is_admin' => true,
        ])) {
            return back()->withInput($request->only('usuario'))->with('mensaje', 'El usuario o la contraseña no son correctos.');
        }

        $request->session()->regenerate();

        return redirect()->route('admin.dashboard');
    }

    public function salir(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('acceso');
    }
}
