@extends('layouts.app')
@section('title', 'Reportes de participación | Bienestar SENA')
@section('sidebar', true)
@section('content')
<div class="animate-fade-up" style="display:flex;justify-content:space-between;align-items:flex-end;gap:1rem;flex-wrap:wrap;">
    <div>
        <span class="eyebrow" style="color:#5a9070;">Indicadores</span>
        <h1 style="margin-top:.375rem;font-size:1.6rem;font-weight:800;color:#e8f5ee;">Reportes de bienestar</h1>
        <p style="margin-top:.25rem;font-size:.875rem;color:#5a9070;">Inscripciones, asistencia por centro y evaluación de satisfacción.</p>
    </div>
    <a class="btn-primary" href="{{ route('admin.reportes.exportar', ['anio' => $anio]) }}">Descargar CSV</a>
</div>

<form method="GET" action="{{ route('admin.reportes') }}" class="card animate-fade-up-2" style="display:flex;align-items:flex-end;gap:.75rem;margin:1.25rem 0;">
    <label style="display:flex;flex-direction:column;gap:.375rem;font-size:.8rem;color:#7aaa90;">Año
        <input class="field" type="number" name="anio" min="2020" max="2100" value="{{ $anio }}" required>
    </label>
    <button class="btn-primary" type="submit">Consultar</button>
</form>

<div class="card animate-fade-up-3" style="padding:0;overflow:hidden;">
    <div style="overflow-x:auto;">
        <table class="data-table">
            <thead><tr><th>Evento</th><th>Fecha</th><th>Centro</th><th>Funcionarios</th><th>Inscritos</th><th>Asistentes</th><th>% inscritos</th><th>% asistencia</th><th>Satisfacción (1-5)</th></tr></thead>
            <tbody>
                @forelse($filas as $fila)
                <tr>
                    <td>{{ $fila['evento'] }}</td>
                    <td>{{ $fila['fecha'] }}</td>
                    <td>{{ $fila['centro'] }}</td>
                    <td>{{ $fila['funcionarios'] }}</td>
                    <td>{{ $fila['inscritos'] }}</td>
                    <td>{{ $fila['asistentes'] }}</td>
                    <td>{{ number_format($fila['porcentaje_inscritos'], 2) }}%</td>
                    <td>{{ number_format($fila['porcentaje_asistencia'], 2) }}%</td>
                    <td>{{ $fila['satisfaccion'] === null ? '—' : number_format($fila['satisfaccion'], 2) }}</td>
                </tr>
                @empty
                <tr><td colspan="9" style="text-align:center;padding:2rem;">No hay actividades para {{ $anio }}.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<p style="margin-top:.75rem;font-size:.75rem;color:#5a9070;">% inscritos = inscritos ÷ funcionarios activos del centro. % asistencia = asistentes ÷ inscritos. El promedio de satisfacción usa respuestas numéricas de preguntas de escala 1–5.</p>
@endsection
