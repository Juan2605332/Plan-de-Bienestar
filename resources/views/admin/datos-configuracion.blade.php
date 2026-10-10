@extends('layouts.app')
@section('title', 'Actualización de datos | Bienestar SENA')
@section('sidebar', true)
@section('content')
<div class="animate-fade-up">
    <span class="eyebrow" style="color:#5a9070;">Funcionarios</span>
    <h1 style="margin-top:.375rem;font-size:1.6rem;font-weight:800;color:#e8f5ee;">Actualización de datos</h1>
    <p style="margin-top:.25rem;font-size:.875rem;color:#5a9070;">Define la ventana anual de edición y registra excepciones autorizadas.</p>
</div>

<section class="card animate-fade-up-2" style="max-width:760px;margin-top:1.5rem;">
    <h2 style="font-size:1.05rem;font-weight:700;color:#e8f5ee;">Período de actualización</h2>
    @if($periodo)
    <p style="margin-top:.5rem;font-size:.85rem;color:#7aaa90;">
        Actual: <strong style="color:#e8f5ee;">{{ $periodo->nombre }}</strong>,
        {{ $periodo->fecha_inicio->format('d/m/Y H:i') }} — {{ $periodo->fecha_cierre->format('d/m/Y H:i') }}.
        Estado: {{ $periodo->estaAbierto() ? 'abierto' : 'cerrado' }}.
    </p>
    @else
    <p style="margin-top:.5rem;font-size:.85rem;color:#7aaa90;">Aún no se ha configurado un período. Los perfiles permanecen de solo lectura hasta que se habilite uno.</p>
    @endif

    <form method="POST" action="{{ route('admin.datos.periodo.guardar') }}" style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-top:1rem;">
        @csrf
        <label style="grid-column:1/-1;display:flex;flex-direction:column;gap:.375rem;font-size:.8rem;color:#7aaa90;">Nombre del período
            <input class="field" name="nombre" required maxlength="150" placeholder="Ej. Actualización anual 2027" value="{{ old('nombre') }}">
        </label>
        <label style="display:flex;flex-direction:column;gap:.375rem;font-size:.8rem;color:#7aaa90;">Inicio
            <input class="field" name="fecha_inicio" type="datetime-local" required value="{{ old('fecha_inicio') }}">
        </label>
        <label style="display:flex;flex-direction:column;gap:.375rem;font-size:.8rem;color:#7aaa90;">Cierre
            <input class="field" name="fecha_cierre" type="datetime-local" required value="{{ old('fecha_cierre') }}">
        </label>
        <div style="grid-column:1/-1;display:flex;justify-content:flex-end;">
            <button class="btn-primary" type="submit">Guardar y activar período</button>
        </div>
    </form>
</section>

<section class="card animate-fade-up-3" style="max-width:760px;margin-top:1rem;">
    <h2 style="font-size:1.05rem;font-weight:700;color:#e8f5ee;">Autorizar una corrección fuera del período</h2>
    <p style="margin-top:.375rem;font-size:.82rem;color:#7aaa90;">La autorización permite guardar una única actualización y queda registrada junto con el motivo.</p>
    <form method="POST" action="{{ route('admin.datos.autorizar') }}" style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-top:1rem;">
        @csrf
        <label style="display:flex;flex-direction:column;gap:.375rem;font-size:.8rem;color:#7aaa90;">Cédula del funcionario
            <input class="field" name="cedula" required maxlength="20" value="{{ old('cedula') }}">
        </label>
        <label style="display:flex;flex-direction:column;gap:.375rem;font-size:.8rem;color:#7aaa90;">Autorización válida hasta
            <input class="field" name="expira_en" type="datetime-local" required value="{{ old('expira_en') }}">
        </label>
        <label style="grid-column:1/-1;display:flex;flex-direction:column;gap:.375rem;font-size:.8rem;color:#7aaa90;">Motivo de la autorización
            <textarea class="field" name="motivo" rows="3" required maxlength="1000">{{ old('motivo') }}</textarea>
        </label>
        <div style="grid-column:1/-1;display:flex;justify-content:flex-end;">
            <button class="btn-primary" type="submit">Autorizar actualización</button>
        </div>
    </form>
</section>

<section class="card animate-fade-up-4" style="margin-top:1rem;">
    <h2 style="font-size:1.05rem;font-weight:700;color:#e8f5ee;">Autorizaciones recientes</h2>
    <div style="overflow-x:auto;margin-top:.75rem;">
        <table class="data-table">
            <thead><tr><th>Funcionario</th><th>Motivo</th><th>Vence</th><th>Uso</th></tr></thead>
            <tbody>
                @forelse($autorizaciones as $autorizacion)
                <tr>
                    <td>{{ $autorizacion->funcionario->nombres }} {{ $autorizacion->funcionario->apellidos }} ({{ $autorizacion->funcionario->cedula }})</td>
                    <td>{{ $autorizacion->motivo }}</td>
                    <td>{{ $autorizacion->expira_en->format('d/m/Y H:i') }}</td>
                    <td>{{ $autorizacion->usado_en?->format('d/m/Y H:i') ?? 'Pendiente' }}</td>
                </tr>
                @empty
                <tr><td colspan="4">No hay autorizaciones registradas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
