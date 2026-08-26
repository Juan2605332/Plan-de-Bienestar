<?php

namespace App\Http\Middleware;

use App\Models\FuncionarioPerfil;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateFuncionario
{
    public function handle(Request $request, Closure $next): Response
    {
        $funcionarioId = $request->session()->get('funcionario_id');

        if ($funcionarioId === null || ! FuncionarioPerfil::whereKey($funcionarioId)->where('activo', true)->exists()) {
            $request->session()->forget('funcionario_id');

            return redirect()->route('acceso')->with('mensaje', 'Inicia sesión para continuar.');
        }

        return $next($request);
    }
}
