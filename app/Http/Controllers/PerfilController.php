<?php

namespace App\Http\Controllers;

use App\Models\FuncionarioPerfil;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PerfilController extends Controller
{
    public function foto(): StreamedResponse
    {
        $rutaFoto = Auth::user()->profile_photo_path;
        abort_if($rutaFoto === null || ! Storage::disk('public')->exists($rutaFoto), 404);

        return Storage::disk('public')->response($rutaFoto);
    }

    public function actualizar(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $funcionario = FuncionarioPerfil::with('familiares')->findOrFail($request->session()->get('funcionario_id'));

        abort_unless(strcasecmp($funcionario->email ?? '', $user->email) === 0, 403);

        $data = $request->validate([
            'nombres' => ['required', 'string', 'max:100'],
            'apellidos' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email,'.$user->id],
            'telefono' => ['required', 'string', 'max:25'],
            'direccion_residencia' => ['required', 'string', 'max:255'],
            'eps' => ['required', 'string', 'max:100'],
            'fondo_pension' => ['required', 'string', 'max:100'],
            'talla_camisa' => ['required', 'string', 'max:10'],
            'talla_pantalon' => ['required', 'string', 'max:10'],
            'talla_calzado' => ['required', 'string', 'max:10'],
            'profile_color' => ['required', 'in:#1d6b3d,#2563eb,#7c3aed,#ea580c'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
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

        $fotoAnterior = $user->profile_photo_path;
        $fotoNueva = null;

        if ($request->hasFile('profile_photo')) {
            $fotoNueva = $request->file('profile_photo')->store('profiles', 'public');
            if ($fotoNueva === false) {
                throw new RuntimeException('No fue posible guardar la foto de perfil.');
            }
        }

        try {
            DB::transaction(function () use ($data, $funcionario, $user, $fotoNueva): void {
                $nombres = trim($data['nombres']);
                $apellidos = trim($data['apellidos']);
                $email = strtolower($data['email']);

                $funcionario->update([
                    'nombres' => $nombres,
                    'apellidos' => $apellidos,
                    'email' => $email,
                    'telefono' => $data['telefono'],
                    'direccion_residencia' => $data['direccion_residencia'],
                    'eps' => $data['eps'],
                    'fondo_pension' => $data['fondo_pension'],
                    'talla_camisa' => $data['talla_camisa'],
                    'talla_pantalon' => $data['talla_pantalon'],
                    'talla_calzado' => $data['talla_calzado'],
                    'es_padre_madre' => collect($data['familiares'] ?? [])
                        ->contains(fn (array $familiar): bool => in_array($familiar['parentesco'], ['HIJO', 'HIJASTRO'], true)),
                ]);
                $funcionario->familiares()->delete();

                foreach ($data['familiares'] ?? [] as $familiar) {
                    $funcionario->familiares()->create($familiar + [
                        'es_a_cargo' => (bool) ($familiar['es_a_cargo'] ?? false),
                    ]);
                }

                $user->update([
                    'name' => $nombres.' '.$apellidos,
                    'email' => $email,
                    'profile_color' => $data['profile_color'],
                    ...($fotoNueva !== null ? ['profile_photo_path' => $fotoNueva] : []),
                ]);
            });
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

        Auth::user()->update([
            'password' => Hash::make($data['password']),
        ]);
        $request->session()->regenerate();

        return back()->with('success', 'Tu contraseña se actualizó correctamente.');
    }
}
