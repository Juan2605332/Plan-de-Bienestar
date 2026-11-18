@extends('layouts.app')

@section('title', 'Mi perfil — Bienestar SENA')
@section('sidebar', 'admin')

@section('content')
    <div style="max-width:900px;margin:0 auto;">
        <div style="margin-bottom:1.5rem;">
            <p class="eyebrow" style="color:var(--lime);margin-bottom:.4rem;">Cuenta personal</p>
            <h1 style="font-size:1.5rem;font-weight:800;color:var(--text-light);">Mi perfil</h1>
            <p style="font-size:.875rem;color:var(--text-muted-dark);margin-top:.35rem;">Actualiza tus datos, personaliza tu foto y administra tu contraseña.</p>
        </div>

        <section class="card" style="margin-bottom:1.25rem;">
            <h2 style="font-size:1rem;font-weight:700;color:var(--text-light);margin-bottom:1.25rem;">Datos y personalización</h2>
            <form action="{{ route('admin.perfil.actualizar') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div style="display:flex;align-items:center;gap:1rem;flex-wrap:wrap;margin-bottom:1.25rem;">
                    <img id="foto-perfil-preview" src="{{ $usuario->profile_photo_path ? route('admin.perfil.foto') : '' }}" alt="Foto de perfil" style="width:88px;height:88px;object-fit:cover;border-radius:50%;border:3px solid {{ $usuario->profile_color }};{{ $usuario->profile_photo_path ? '' : 'display:none;' }}">
                    <span id="foto-perfil-placeholder" style="width:88px;height:88px;display:{{ $usuario->profile_photo_path ? 'none' : 'flex' }};align-items:center;justify-content:center;border-radius:50%;background:{{ $usuario->profile_color }};color:#fff;font-size:1.75rem;font-weight:700;">{{ strtoupper(substr($usuario->name, 0, 1)) }}</span>
                    <label style="display:flex;flex-direction:column;gap:.45rem;flex:1;min-width:220px;color:var(--text-light);font-size:.8rem;font-weight:600;">
                        Foto de perfil
                        <input id="foto-perfil-input" type="file" name="profile_photo" accept="image/jpeg,image/png,image/webp" class="field" style="padding:.5rem;">
                        <span style="font-size:.7rem;font-weight:400;color:var(--text-muted-dark);">JPG, PNG o WebP; máximo 2 MB.</span>
                    </label>
                </div>

                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1rem;">
                    <label style="display:flex;flex-direction:column;gap:.45rem;color:var(--text-light);font-size:.8rem;font-weight:600;">
                        Nombre
                        <input type="text" name="name" value="{{ old('name', $usuario->name) }}" maxlength="255" required class="field" autocomplete="name">
                    </label>
                    <label style="display:flex;flex-direction:column;gap:.45rem;color:var(--text-light);font-size:.8rem;font-weight:600;">
                        Correo electrónico
                        <input type="email" name="email" value="{{ old('email', $usuario->email) }}" maxlength="255" required class="field" autocomplete="email">
                    </label>
                    <label style="display:flex;flex-direction:column;gap:.45rem;color:var(--text-light);font-size:.8rem;font-weight:600;">
                        Color del perfil
                        <select name="profile_color" class="field">
                            @foreach(['#1d6b3d' => 'Verde SENA', '#2563eb' => 'Azul', '#7c3aed' => 'Violeta', '#ea580c' => 'Naranja'] as $color => $nombreColor)
                                <option value="{{ $color }}" @selected(old('profile_color', $usuario->profile_color) === $color)>{{ $nombreColor }}</option>
                            @endforeach
                        </select>
                    </label>
                </div>

                <div style="display:flex;justify-content:flex-end;margin-top:1.25rem;">
                    <button type="submit" class="btn-primary">Guardar perfil</button>
                </div>
            </form>
        </section>

        <section class="card">
            <h2 style="font-size:1rem;font-weight:700;color:var(--text-light);margin-bottom:.35rem;">Cambiar contraseña</h2>
            <p style="font-size:.8rem;color:var(--text-muted-dark);margin-bottom:1rem;">Confirma tu contraseña actual. La nueva debe tener al menos 10 caracteres.</p>
            <form action="{{ route('admin.perfil.contrasena') }}" method="POST" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:1rem;align-items:end;">
                @csrf
                @method('PUT')
                <label style="display:flex;flex-direction:column;gap:.45rem;color:var(--text-light);font-size:.8rem;font-weight:600;">
                    Contraseña actual
                    <input type="password" name="current_password" required autocomplete="current-password" class="field">
                </label>
                <label style="display:flex;flex-direction:column;gap:.45rem;color:var(--text-light);font-size:.8rem;font-weight:600;">
                    Nueva contraseña
                    <input type="password" name="password" required minlength="10" autocomplete="new-password" class="field">
                </label>
                <label style="display:flex;flex-direction:column;gap:.45rem;color:var(--text-light);font-size:.8rem;font-weight:600;">
                    Confirmar contraseña
                    <input type="password" name="password_confirmation" required minlength="10" autocomplete="new-password" class="field">
                </label>
                <button type="submit" class="btn-primary">Actualizar contraseña</button>
            </form>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        const fotoInput = document.getElementById('foto-perfil-input');
        const fotoPreview = document.getElementById('foto-perfil-preview');
        const fotoPlaceholder = document.getElementById('foto-perfil-placeholder');
        let fotoTemporal = null;

        fotoInput.addEventListener('change', () => {
            const archivo = fotoInput.files[0];
            if (!archivo) return;

            if (fotoTemporal) URL.revokeObjectURL(fotoTemporal);
            fotoTemporal = URL.createObjectURL(archivo);
            fotoPreview.src = fotoTemporal;
            fotoPreview.style.display = 'block';
            fotoPlaceholder.style.display = 'none';
        });
    </script>
@endpush
