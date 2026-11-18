<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminPerfilController extends Controller
{
    public function index(): View
    {
        return view('admin.perfil', ['usuario' => Auth::user()]);
    }

    public function actualizar(Request $request): RedirectResponse
    {
        $usuario = Auth::user();
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($usuario->id)],
            'profile_color' => ['required', 'in:#1d6b3d,#2563eb,#7c3aed,#ea580c'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
        ]);

        $fotoAnterior = $usuario->profile_photo_path;
        $fotoNueva = null;

        if ($request->hasFile('profile_photo')) {
            $fotoNueva = $request->file('profile_photo')->store('profiles', 'public');
            if ($fotoNueva === false) {
                throw new RuntimeException('No fue posible guardar la foto de perfil.');
            }
        }

        try {
            $usuario->update([
                'name' => trim($data['name']),
                'email' => strtolower($data['email']),
                'profile_color' => $data['profile_color'],
                ...($fotoNueva !== null ? ['profile_photo_path' => $fotoNueva] : []),
            ]);
        } catch (\Throwable $exception) {
            if ($fotoNueva !== null && ! Storage::disk('public')->delete($fotoNueva)) {
                Log::warning('No se pudo limpiar una foto de perfil tras fallar su actualización.', ['path' => $fotoNueva]);
            }

            throw $exception;
        }

        if ($fotoNueva !== null && $fotoAnterior !== null && ! Storage::disk('public')->delete($fotoAnterior)) {
            Log::warning('No se pudo eliminar la foto de perfil anterior.', ['path' => $fotoAnterior]);
        }

        return back()->with('success', 'Tu perfil se actualizó correctamente.');
    }

    public function actualizarContrasena(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(10)],
        ]);

        Auth::user()->update(['password' => Hash::make($data['password'])]);
        $request->session()->regenerate();

        return back()->with('success', 'Tu contraseña se actualizó correctamente.');
    }

    public function foto(): StreamedResponse
    {
        $rutaFoto = Auth::user()->profile_photo_path;
        abort_if($rutaFoto === null || ! Storage::disk('public')->exists($rutaFoto), 404);

        return Storage::disk('public')->response($rutaFoto);
    }
}
