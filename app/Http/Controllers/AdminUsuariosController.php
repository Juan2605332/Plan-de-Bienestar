<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AdminUsuariosController extends Controller
{
    public function index(Request $request): View
    {
        $usuarios = User::query()
            ->where('is_admin', false)
            ->with('funcionario')
            ->when($request->filled('buscar'), function ($query) use ($request): void {
                $termino = '%'.$request->string('buscar')->trim().'%';
                $query->where(function ($subquery) use ($termino): void {
                    $subquery->where('name', 'like', $termino)
                        ->orWhere('email', 'like', $termino);
                });
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.usuarios', compact('usuarios'));
    }

    public function actualizarEstado(Request $request, User $user): RedirectResponse
    {
        abort_if($user->is_admin, 404);

        $data = $request->validate([
            'activo' => ['required', 'boolean'],
        ]);

        $user->update(['activo' => $data['activo']]);

        return back()->with('success', $data['activo']
            ? 'Cuenta activada correctamente.'
            : 'Cuenta desactivada. El usuario ya no podrá iniciar sesión.');
    }

    public function actualizarContrasena(Request $request, User $user): RedirectResponse
    {
        abort_if($user->is_admin, 404);

        $data = $request->validate([
            'password' => ['required', 'confirmed', Password::min(10)],
        ]);

        $user->forceFill([
            'password' => Hash::make($data['password']),
            'remember_token' => Str::random(60),
        ])->save();

        return back()->with('success', "La contraseña de {$user->name} se actualizó correctamente.");
    }
}
