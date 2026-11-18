@extends('layouts.app')

@section('title', 'Usuarios — Bienestar SENA')
@section('sidebar', 'admin')

@section('content')
    <div style="max-width:1180px;margin:0 auto;">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;flex-wrap:wrap;margin-bottom:1.5rem;">
            <div>
                <p class="eyebrow" style="color:var(--lime);margin-bottom:.4rem;">Administración</p>
                <h1 style="font-size:1.5rem;font-weight:800;color:var(--text-light);">Cuentas de funcionarios</h1>
                <p style="font-size:.875rem;color:var(--text-muted-dark);margin-top:.35rem;">Activa o desactiva el acceso y restablece contraseñas de usuarios normales.</p>
            </div>
            <span class="badge-green">{{ $usuarios->total() }} cuentas</span>
        </div>

        <form method="GET" action="{{ route('admin.usuarios.index') }}" style="display:flex;gap:.625rem;max-width:560px;margin-bottom:1.25rem;">
            <input type="search" name="buscar" value="{{ request('buscar') }}" class="field" placeholder="Buscar por nombre o correo" aria-label="Buscar usuarios">
            <button type="submit" class="btn-primary">Buscar</button>
            @if (request()->filled('buscar'))
                <a href="{{ route('admin.usuarios.index') }}" class="btn-ghost">Limpiar</a>
            @endif
        </form>

        <div class="card" style="padding:0;overflow:hidden;">
            @if ($usuarios->isEmpty())
                <p style="padding:2rem;text-align:center;color:var(--text-muted-dark);">No se encontraron cuentas normales.</p>
            @else
                <div style="overflow-x:auto;">
                    <table style="width:100%;border-collapse:collapse;min-width:800px;">
                        <thead>
                            <tr style="background:rgba(255,255,255,.03);text-align:left;">
                                <th style="padding:.875rem 1rem;color:var(--text-muted-dark);font-size:.7rem;text-transform:uppercase;letter-spacing:.06em;">Funcionario</th>
                                <th style="padding:.875rem 1rem;color:var(--text-muted-dark);font-size:.7rem;text-transform:uppercase;letter-spacing:.06em;">Acceso</th>
                                <th style="padding:.875rem 1rem;color:var(--text-muted-dark);font-size:.7rem;text-transform:uppercase;letter-spacing:.06em;">Estado</th>
                                <th style="padding:.875rem 1rem;color:var(--text-muted-dark);font-size:.7rem;text-transform:uppercase;letter-spacing:.06em;">Activar / desactivar</th>
                                <th style="padding:.875rem 1rem;color:var(--text-muted-dark);font-size:.7rem;text-transform:uppercase;letter-spacing:.06em;">Restablecer contraseña</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($usuarios as $usuario)
                                <tr style="border-top:1px solid var(--border-dark);">
                                    <td style="padding:1rem;color:var(--text-light);">
                                        <strong style="display:block;font-size:.875rem;">{{ $usuario->funcionario?->nombres }} {{ $usuario->funcionario?->apellidos ?: $usuario->name }}</strong>
                                        @if ($usuario->funcionario)
                                            <span style="font-size:.75rem;color:var(--text-muted-dark);">Cédula: {{ $usuario->funcionario->cedula }}</span>
                                        @endif
                                    </td>
                                    <td style="padding:1rem;color:var(--text-muted-dark);font-size:.8125rem;">{{ $usuario->email }}</td>
                                    <td style="padding:1rem;">
                                        <span class="badge-green" style="{{ $usuario->activo ? '' : 'background:rgba(239,68,68,.1);border-color:rgba(239,68,68,.2);color:#f87171;' }}">
                                            {{ $usuario->activo ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>
                                    <td style="padding:1rem;">
                                        <form method="POST" action="{{ route('admin.usuarios.estado', $usuario) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="activo" value="{{ $usuario->activo ? 0 : 1 }}">
                                            <button type="submit" class="{{ $usuario->activo ? 'btn-ghost' : 'btn-primary' }}" style="padding:.45rem .7rem;font-size:.75rem;">
                                                {{ $usuario->activo ? 'Desactivar' : 'Activar' }}
                                            </button>
                                        </form>
                                    </td>
                                    <td style="padding:1rem;">
                                        <form method="POST" action="{{ route('admin.usuarios.contrasena', $usuario) }}" style="display:grid;grid-template-columns:1fr 1fr auto;gap:.4rem;min-width:410px;">
                                            @csrf
                                            @method('PUT')
                                            <input type="password" name="password" class="field" placeholder="Nueva (10+ caracteres)" minlength="10" required autocomplete="new-password" aria-label="Nueva contraseña para {{ $usuario->name }}">
                                            <input type="password" name="password_confirmation" class="field" placeholder="Confirmar" minlength="10" required autocomplete="new-password" aria-label="Confirmar contraseña para {{ $usuario->name }}">
                                            <button type="submit" class="btn-primary" style="padding:.45rem .65rem;font-size:.75rem;">Guardar</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div style="padding:1rem;border-top:1px solid var(--border-dark);">{{ $usuarios->links() }}</div>
            @endif
        </div>
    </div>
@endsection
