<?php

namespace App\Http\Middleware;

use App\Models\FuncionarioPerfil;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $funcionario = FuncionarioPerfil::find($request->session()->get('funcionario_id'));
        $cedulas = config('app.admin_cedulas', []);

        abort_unless($funcionario !== null && in_array($funcionario->cedula, $cedulas, true), 403);

        return $next($request);
    }
}
