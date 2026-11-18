<?php

namespace App\Http\Middleware;

use App\Models\FuncionarioPerfil;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateFuncionario
{
    public function handle(Request $request, Closure $next): Response
    {
        $funcionarioId = $request->session()->get('funcionario_id');

        if (
            ! Auth::check()
            || ! Auth::user()->activo
            || $funcionarioId === null
            || ! FuncionarioPerfil::whereKey($funcionarioId)->where('activo', true)->exists()
        ) {
            Auth::logout();
            $request->session()->forget('funcionario_id');

            return redirect()->route('acceso')->with('mensaje', 'Inicia sesión para continuar.');
        }

        return $next($request);
    }
}
