@extends('layouts.app')
@section('title', 'Nuevo período | Bienestar SENA')
@section('sidebar', true)
@section('content')
<div style="max-width:600px;" class="animate-fade-up">
    <div style="margin-bottom:1.5rem;">
        <span class="eyebrow" style="color:#5a9070;">Configuración</span>
        <h1 style="margin-top:.375rem;font-size:1.6rem;font-weight:800;color:#e8f5ee;letter-spacing:-.02em;">Crear período</h1>
        <p style="margin-top:.25rem;font-size:.875rem;color:#5a9070;">Define la ventana de inscripción para tus actividades.</p>
    </div>

    <form class="animate-fade-up-2" method="POST" action="{{ route('admin.periodos.guardar') }}" style="display:flex;flex-direction:column;gap:1rem;">
        @csrf
        <div style="background:var(--surface-card);border:1px solid var(--border-dark);border-radius:1rem;padding:1.5rem;">
            <p style="font-size:.7rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:#5a9070;margin-bottom:1.125rem;display:flex;align-items:center;gap:.625rem;">
                Detalles del período
                <span style="flex:1;height:1px;background:var(--border-dark);display:block;"></span>
            </p>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                <label style="display:flex;flex-direction:column;gap:.375rem;font-size:.8rem;font-weight:600;color:#7aaa90;grid-column:span 2;">
                    Nombre del período
                    <input class="field" name="nombre" value="{{ old('nombre') }}" required placeholder="Ej. Primer semestre 2026">
                </label>
                <label style="display:flex;flex-direction:column;gap:.375rem;font-size:.8rem;font-weight:600;color:#7aaa90;">
                    Año
                    <input class="field" name="anio" type="number" value="{{ old('anio', now()->year) }}" required>
                </label>
                <span></span>
                <label style="display:flex;flex-direction:column;gap:.375rem;font-size:.8rem;font-weight:600;color:#7aaa90;">
                    Fecha de inicio
                    <input class="field" name="fecha_inicio" type="datetime-local" value="{{ old('fecha_inicio') }}" required>
                </label>
                <label style="display:flex;flex-direction:column;gap:.375rem;font-size:.8rem;font-weight:600;color:#7aaa90;">
                    Fecha de cierre
                    <input class="field" name="fecha_cierre" type="datetime-local" value="{{ old('fecha_cierre') }}" required>
                </label>
            </div>
        </div>

        <div style="display:flex;justify-content:flex-end;gap:.75rem;" class="animate-fade-up-3">
            <a href="{{ route('admin.dashboard') }}" class="btn-ghost">Cancelar</a>
            <button type="submit" class="btn-primary">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Guardar período
            </button>
        </div>
    </form>
</div>
@endsection
