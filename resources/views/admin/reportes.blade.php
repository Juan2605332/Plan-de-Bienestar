@extends('layouts.app')
@section('title', 'Reportes de participación | Bienestar SENA')
@section('sidebar', true)
@section('content')
@php
    $reporte = collect($filas);
    $promediosPorEvento = $reporte
        ->filter(fn ($fila) => $fila['satisfaccion'] !== null)
        ->unique('evento_id')
        ->pluck('satisfaccion');
@endphp
<style>
    .reports-page { color: #e8f5ee; }
    .reports-heading { color: #f4fbf6; }
    .reports-subtitle { color: #b6d2c1; }
    .reports-filter {
        display: flex;
        align-items: flex-end;
        flex-wrap: wrap;
        gap: .875rem;
        margin: 1.5rem 0;
        padding: 1rem 1.25rem;
        background: #142818;
        border: 1px solid #31513e;
        border-radius: 1rem;
    }
    .reports-filter label {
        display: flex;
        flex-direction: column;
        gap: .4rem;
        color: #e0eee5;
        font-size: .82rem;
        font-weight: 600;
    }
    .reports-filter .field { min-width: 150px; }
    .reports-summary {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 1rem;
        margin-bottom: 1.25rem;
    }
    .reports-stat {
        min-width: 0;
        padding: 1.1rem 1.25rem;
        background: #fff;
        border: 1px solid #dce8df;
        border-radius: .9rem;
        box-shadow: 0 8px 24px rgba(0,0,0,.12);
    }
    .reports-stat-label {
        display: block;
        color: #52665a;
        font-size: .75rem;
        font-weight: 700;
        letter-spacing: .045em;
        text-transform: uppercase;
    }
    .reports-stat-value {
        display: block;
        margin-top: .35rem;
        color: #123d29;
        font-size: 1.7rem;
        font-weight: 800;
        line-height: 1.15;
        overflow-wrap: anywhere;
    }
    .reports-panel {
        overflow: hidden;
        background: #fff;
        border: 1px solid #dce8df;
        border-radius: 1rem;
        box-shadow: 0 8px 24px rgba(0,0,0,.12);
    }
    .reports-panel-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: .5rem;
        padding: 1.1rem 1.25rem;
        border-bottom: 1px solid #dce8df;
    }
    .reports-panel-heading h2 {
        margin: 0;
        color: #173d2b;
        font-size: 1rem;
        font-weight: 800;
    }
    .reports-panel-heading span {
        color: #52665a;
        font-size: .8rem;
    }
    .reports-table-wrap { width: 100%; overflow-x: auto; }
    .reports-table {
        width: 100%;
        min-width: 1050px;
        border-collapse: separate;
        border-spacing: 0;
        color: #26382c;
        font-size: .82rem;
        text-align: left;
    }
    .reports-table th {
        position: sticky;
        top: 0;
        z-index: 1;
        padding: .8rem .9rem;
        background: #edf4ef;
        border-bottom: 1px solid #cfddd2;
        color: #294b36;
        font-size: .7rem;
        font-weight: 800;
        letter-spacing: .035em;
        line-height: 1.35;
        text-transform: uppercase;
        white-space: nowrap;
    }
    .reports-table td {
        padding: .85rem .9rem;
        border-bottom: 1px solid #e5ece7;
        color: #26382c;
        vertical-align: middle;
    }
    .reports-table tbody tr:nth-child(even) { background: #f7faf8; }
    .reports-table tbody tr:hover { background: #eef7f0; }
    .reports-table tbody tr:last-child td { border-bottom: 0; }
    .reports-table .report-event { min-width: 190px; color: #173d2b; font-weight: 700; }
    .reports-table .report-number { font-variant-numeric: tabular-nums; white-space: nowrap; }
    .reports-table .report-empty { padding: 2.5rem 1rem; color: #52665a; text-align: center; }
    .reports-note {
        margin: 1rem 0 0;
        color: #d3e4d8;
        font-size: .78rem;
        line-height: 1.65;
    }
    @media (max-width: 900px) {
        .reports-summary { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width: 540px) {
        .reports-summary { gap: .65rem; }
        .reports-stat { padding: .9rem; }
        .reports-stat-value { font-size: 1.4rem; }
        .reports-filter { align-items: stretch; }
        .reports-filter label, .reports-filter .field, .reports-filter button { width: 100%; }
    }
</style>

<div class="reports-page animate-fade-up">
    <div style="display:flex;justify-content:space-between;align-items:flex-end;gap:1rem;flex-wrap:wrap;">
        <div>
            <span class="eyebrow" style="color:#a8e063;">Indicadores</span>
            <h1 class="reports-heading" style="margin-top:.375rem;font-size:1.7rem;font-weight:800;">Reportes de bienestar</h1>
            <p class="reports-subtitle" style="margin-top:.35rem;font-size:.9rem;">Inscripciones, asistencia por centro y evaluación de satisfacción.</p>
        </div>
        <a class="btn-primary" href="{{ route('admin.reportes.exportar', ['anio' => $anio]) }}">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Descargar CSV
        </a>
    </div>

    <form method="GET" action="{{ route('admin.reportes') }}" class="reports-filter animate-fade-up-2">
        <label for="reporte-anio">Año
            <input id="reporte-anio" class="field" type="number" name="anio" min="2020" max="2100" value="{{ $anio }}" required>
        </label>
        <button class="btn-primary" type="submit">Consultar</button>
    </form>

    <div class="reports-summary animate-fade-up-2" aria-label="Resumen del reporte">
        <div class="reports-stat">
            <span class="reports-stat-label">Eventos reportados</span>
            <strong class="reports-stat-value">{{ $reporte->pluck('evento_id')->unique()->count() }}</strong>
        </div>
        <div class="reports-stat">
            <span class="reports-stat-label">Inscripciones</span>
            <strong class="reports-stat-value">{{ number_format($reporte->sum('inscritos')) }}</strong>
        </div>
        <div class="reports-stat">
            <span class="reports-stat-label">Asistencias registradas</span>
            <strong class="reports-stat-value">{{ number_format($reporte->sum('asistentes')) }}</strong>
        </div>
        <div class="reports-stat">
            <span class="reports-stat-label">Satisfacción promedio</span>
            <strong class="reports-stat-value">{{ $promediosPorEvento->isEmpty() ? '—' : number_format($promediosPorEvento->avg(), 2).' / 5' }}</strong>
        </div>
    </div>

    <section class="reports-panel animate-fade-up-3" aria-labelledby="reporte-detalle">
        <div class="reports-panel-heading">
            <h2 id="reporte-detalle">Detalle por evento y centro de formación</h2>
            <span>{{ $reporte->count() }} {{ $reporte->count() === 1 ? 'registro' : 'registros' }} · {{ $anio }}</span>
        </div>
        <div class="reports-table-wrap">
            <table class="reports-table">
                <thead>
                    <tr>
                        <th>Evento</th>
                        <th>Fecha</th>
                        <th>Centro</th>
                        <th>Funcionarios activos</th>
                        <th>Inscritos</th>
                        <th>Asistentes</th>
                        <th>% inscritos</th>
                        <th>% asistencia</th>
                        <th>Satisfacción (1–5)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($filas as $fila)
                        <tr>
                            <td class="report-event">{{ $fila['evento'] }}</td>
                            <td class="report-number">{{ $fila['fecha'] }}</td>
                            <td>{{ $fila['centro'] }}</td>
                            <td class="report-number">{{ number_format($fila['funcionarios']) }}</td>
                            <td class="report-number">{{ number_format($fila['inscritos']) }}</td>
                            <td class="report-number">{{ number_format($fila['asistentes']) }}</td>
                            <td class="report-number">{{ number_format($fila['porcentaje_inscritos'], 2) }}%</td>
                            <td class="report-number">{{ number_format($fila['porcentaje_asistencia'], 2) }}%</td>
                            <td class="report-number">{{ $fila['satisfaccion'] === null ? '—' : number_format($fila['satisfaccion'], 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="report-empty">No hay actividades registradas para {{ $anio }}.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <p class="reports-note">
        <strong>% inscritos:</strong> inscritos ÷ funcionarios activos del centro.
        <strong>% asistencia:</strong> asistentes ÷ inscritos.
        La satisfacción es el promedio de respuestas numéricas a preguntas de escala 1–5.
        En pantallas pequeñas, desliza la tabla horizontalmente para consultar todas las columnas.
    </p>
</div>
@endsection
