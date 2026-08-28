@extends('layouts.app')

@section('title', 'Panel administrativo | Bienestar SENA')

{{-- Activar sidebar --}}
@section('sidebar', true)

@push('scripts')
<script>
const buscadorEventoAdmin = document.getElementById('buscar-evento-admin');
let temporizadorEventoAdmin;
buscadorEventoAdmin?.addEventListener('input', () => {
    clearTimeout(temporizadorEventoAdmin);
    document.getElementById('busqueda-evento-admin-estado').textContent = 'Buscando...';
    temporizadorEventoAdmin = setTimeout(() => {
        const parametros = new URLSearchParams(window.location.search);
        const termino = buscadorEventoAdmin.value.trim();
        termino ? parametros.set('buscar_evento', termino) : parametros.delete('buscar_evento');
        window.location.search = parametros.toString();
    }, 350);
});
</script>
@endpush

@section('content')
<style>
    .kpi-card {
        background: var(--surface-card);
        border: 1px solid var(--border-dark);
        border-radius: 1rem;
        padding: 1.375rem 1.5rem;
        position: relative;
        overflow: hidden;
        transition: transform .2s, box-shadow .2s;
    }
    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 32px rgba(0,0,0,.25);
    }
    .kpi-card-accent {
        background: linear-gradient(135deg, #0d3b22 0%, #103c2c 100%);
        border-color: rgba(168,224,99,.2);
    }
    .kpi-icon {
        width: 38px; height: 38px;
        border-radius: .625rem;
        display: flex; align-items: center; justify-content: center;
        margin-bottom: 1rem;
    }
    .kpi-number {
        font-size: 2.25rem;
        font-weight: 800;
        line-height: 1;
        color: #e8f5ee;
        letter-spacing: -.03em;
    }
    .kpi-label {
        font-size: .75rem;
        color: #5a9070;
        margin-top: .375rem;
    }
    .kpi-deco {
        position: absolute;
        width: 120px; height: 120px;
        border-radius: 50%;
        background: rgba(168,224,99,.05);
        top: -30px; right: -30px;
        pointer-events: none;
    }

    /* Table */
    .data-table { width: 100%; border-collapse: collapse; font-size: .8125rem; }
    .data-table thead tr {
        border-bottom: 1px solid var(--border-dark);
    }
    .data-table thead th {
        padding: .625rem 1rem;
        font-size: .7rem;
        font-weight: 700;
        letter-spacing: .06em;
        text-transform: uppercase;
        color: #5a9070;
        text-align: left;
    }
    .data-table tbody tr {
        border-bottom: 1px solid rgba(30,64,48,.5);
        transition: background .12s;
    }
    .data-table tbody tr:last-child { border-bottom: none; }
    .data-table tbody tr:hover { background: rgba(168,224,99,.04); }
    .data-table td { padding: .8rem 1rem; color: #c4ddd0; vertical-align: middle; }
    .data-table td:first-child { font-weight: 600; color: #e8f5ee; }

    /* Avatar circle */
    .avatar {
        width: 32px; height: 32px;
        border-radius: 50%;
        background: linear-gradient(135deg, #1d6b3d, #23a05a);
        display: inline-flex; align-items: center; justify-content: center;
        font-size: .75rem; font-weight: 700; color: #e8f5ee;
        flex-shrink: 0;
    }

    /* Event card */
    .event-card {
        background: var(--surface-card);
        border: 1px solid var(--border-dark);
        border-radius: .875rem;
        padding: 1.25rem 1.375rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        transition: border-color .15s, transform .15s;
    }
    .event-card:hover {
        border-color: rgba(168,224,99,.25);
        transform: translateX(2px);
    }
    .event-date-badge {
        flex-shrink: 0;
        width: 52px;
        background: rgba(168,224,99,.1);
        border: 1px solid rgba(168,224,99,.15);
        border-radius: .625rem;
        text-align: center;
        padding: .5rem .25rem;
    }

    /* Search */
    .search-bar {
        background: var(--surface-card);
        border: 1px solid var(--border-dark);
        border-radius: .75rem;
        padding: .6rem .875rem;
        color: #e8f5ee;
        font-size: .875rem;
        font-family: 'Inter', sans-serif;
        outline: none;
        transition: border-color .15s, box-shadow .15s;
        width: 100%;
    }
    .search-bar:focus {
        border-color: #4ec483;
        box-shadow: 0 0 0 3px rgba(78,196,131,.1);
    }
    .search-bar::placeholder { color: #3d7055; }

    /* Filter select */
    .filter-select {
        background: var(--surface-card);
        border: 1px solid var(--border-dark);
        border-radius: .625rem;
        padding: .625rem .875rem;
        color: #c4ddd0;
        font-size: .8125rem;
        font-family: 'Inter', sans-serif;
        outline: none;
        transition: border-color .15s;
        width: 100%;
    }
    .filter-select:focus { border-color: #4ec483; }

    /* Section header */
    .section-hd {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
        margin-bottom: 1.25rem;
    }
</style>

{{-- Page header --}}
<div style="margin-bottom:2rem;" class="animate-fade-up">
    <span class="eyebrow" style="color:#5a9070;">Centro de control</span>
    <h1 style="margin-top:.375rem;font-size:1.75rem;font-weight:800;color:#e8f5ee;letter-spacing:-.02em;">Panel administrativo</h1>
    <p style="margin-top:.25rem;font-size:.875rem;color:#5a9070;">Coordina bienestar, participación y comunidad en un solo lugar.</p>
</div>

{{-- KPI Cards --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1rem;margin-bottom:2rem;" class="animate-fade-up-2">
    <div class="kpi-card kpi-card-accent">
        <div class="kpi-deco"></div>
        <div class="kpi-icon" style="background:rgba(168,224,99,.15);">
            <svg width="18" height="18" fill="none" stroke="#a8e063" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
        </div>
        <div class="kpi-number" style="color:#a8e063;">{{ $eventos->count() }}</div>
        <div class="kpi-label">Eventos programados</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon" style="background:rgba(35,160,90,.12);">
            <svg width="18" height="18" fill="none" stroke="#4ec483" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
        <div class="kpi-number">{{ $funcionarios->count() }}</div>
        <div class="kpi-label">Funcionarios activos</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon" style="background:rgba(78,196,131,.1);">
            <svg width="18" height="18" fill="none" stroke="#4ec483" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        </div>
        <div class="kpi-number">{{ $eventos->sum('inscripciones_count') }}</div>
        <div class="kpi-label">Inscripciones totales</div>
    </div>
</div>

{{-- Funcionarios section --}}
<section class="card animate-fade-up-3" style="margin-bottom:1.5rem;">
    <div class="section-hd">
        <div>
            <span class="eyebrow" style="color:#5a9070;">Audiencias</span>
            <h2 style="margin-top:.25rem;font-size:1.125rem;font-weight:700;color:#e8f5ee;">Funcionarios y participación</h2>
            <p style="margin-top:.125rem;font-size:.8rem;color:#5a9070;">Filtra para identificar grupos de cada iniciativa.</p>
        </div>
        <span class="badge-green">{{ $funcionarios->count() }} resultados</span>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.dashboard') }}" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:.75rem;margin-bottom:1.25rem;padding:.875rem;background:rgba(0,0,0,.2);border-radius:.75rem;border:1px solid var(--border-dark);">
        <label style="display:flex;flex-direction:column;gap:.35rem;font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:#5a9070;">
            Género
            <select name="genero" class="filter-select">
                <option value="">Todos</option>
                <option value="MASCULINO" @selected(request('genero') === 'MASCULINO')>Hombres</option>
                <option value="FEMENINO"  @selected(request('genero') === 'FEMENINO')>Mujeres</option>
                <option value="OTRO"      @selected(request('genero') === 'OTRO')>Otro</option>
            </select>
        </label>
        <label style="display:flex;flex-direction:column;gap:.35rem;font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:#5a9070;">
            Edad mínima
            <input name="edad_min" type="number" min="0" max="120" value="{{ request('edad_min') }}" class="filter-select">
        </label>
        <label style="display:flex;flex-direction:column;gap:.35rem;font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:#5a9070;">
            Edad máxima
            <input name="edad_max" type="number" min="0" max="120" value="{{ request('edad_max') }}" class="filter-select">
        </label>
        <label style="display:flex;align-items:center;gap:.5rem;font-size:.8rem;color:#c4ddd0;cursor:pointer;align-self:end;padding-bottom:.5rem;">
            <input name="padres" value="1" type="checkbox" @checked(request()->boolean('padres')) style="width:16px;height:16px;accent-color:#a8e063;"> Padres/madres
        </label>
        <label style="display:flex;align-items:center;gap:.5rem;font-size:.8rem;color:#c4ddd0;cursor:pointer;align-self:end;padding-bottom:.5rem;">
            <input name="a_cargo" value="1" type="checkbox" @checked(request()->boolean('a_cargo')) style="width:16px;height:16px;accent-color:#a8e063;"> Familiares a cargo
        </label>
        <label style="display:flex;align-items:center;gap:.5rem;font-size:.8rem;color:#c4ddd0;cursor:pointer;align-self:end;padding-bottom:.5rem;">
            <input name="cumpleanos" value="1" type="checkbox" @checked(request()->boolean('cumpleanos')) style="width:16px;height:16px;accent-color:#a8e063;"> Cumpleaños mes
        </label>
        <div style="display:flex;gap:.5rem;align-self:end;grid-column: span 2;">
            <button class="btn-primary" style="padding:.55rem 1rem;font-size:.8rem;">Aplicar</button>
            <a href="{{ route('admin.dashboard') }}" class="btn-ghost" style="padding:.55rem 1rem;font-size:.8rem;">Limpiar</a>
        </div>
    </form>

    {{-- Table --}}
    <div style="overflow-x:auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Funcionario</th>
                    <th>Género</th>
                    <th>Nacimiento</th>
                    <th>Familiares</th>
                    <th>A cargo</th>
                    <th>Condición</th>
                </tr>
            </thead>
            <tbody>
                @forelse($funcionarios as $funcionario)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:.625rem;">
                            <span class="avatar">{{ strtoupper(substr($funcionario->nombres, 0, 1)) }}</span>
                            {{ $funcionario->nombres }} {{ $funcionario->apellidos }}
                        </div>
                    </td>
                    <td>{{ $funcionario->genero }}</td>
                    <td>{{ $funcionario->fecha_nacimiento->format('d/m/Y') }}</td>
                    <td>{{ $funcionario->familiares_count }}</td>
                    <td>{{ $funcionario->familiares_a_cargo_count }}</td>
                    <td>
                        {{ $funcionario->es_padre_madre ? 'Padre/madre' : 'Sin hijos' }}
                        @if($funcionario->fecha_nacimiento->isBirthday(now()))
                        <span class="badge-green" style="margin-left:.375rem;">🎂 Hoy</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;color:#3d7055;padding:2rem;">No hay funcionarios con esos filtros.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

{{-- Eventos section --}}
<section class="animate-fade-up-4">
    <div class="section-hd">
        <div>
            <span class="eyebrow" style="color:#5a9070;">Agenda</span>
            <h2 style="margin-top:.25rem;font-size:1.125rem;font-weight:700;color:#e8f5ee;">Eventos programados</h2>
        </div>
        <a href="{{ route('admin.calendario') }}" style="font-size:.8125rem;font-weight:600;color:#a8e063;text-decoration:none;display:flex;align-items:center;gap:.375rem;">
            Ver calendario
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </a>
    </div>

    {{-- Search --}}
    <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1rem;">
        <label for="buscar-evento-admin" class="sr-only">Buscar evento</label>
        <div style="position:relative;flex:1;">
            <svg style="position:absolute;left:.75rem;top:50%;transform:translateY(-50%);color:#3d7055;" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            <input id="buscar-evento-admin" type="search" value="{{ request('buscar_evento') }}" placeholder="Buscar por nombre, descripción o lugar..." class="search-bar" style="padding-left:2.25rem;">
        </div>
        <span id="busqueda-evento-admin-estado" style="font-size:.75rem;color:#3d7055;white-space:nowrap;"></span>
    </div>

    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(340px,1fr));gap:.875rem;">
        @forelse($eventos as $evento)
        <article class="event-card">
            {{-- Date badge --}}
            <div class="event-date-badge">
                <span style="display:block;font-size:1.25rem;font-weight:800;color:#a8e063;line-height:1;">{{ $evento->fecha_evento->format('d') }}</span>
                <span style="display:block;font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#5a9070;margin-top:.125rem;">{{ $evento->fecha_evento->format('M') }}</span>
            </div>
            {{-- Info --}}
            <div style="flex:1;min-width:0;">
                <h3 style="font-size:.9rem;font-weight:700;color:#e8f5ee;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $evento->nombre }}</h3>
                <p style="font-size:.78rem;color:#5a9070;margin-top:.2rem;">
                    <span class="badge-green" style="font-size:.65rem;">{{ $evento->inscripciones_count }} inscritos</span>
                    @if($evento->lugar)&nbsp;· {{ $evento->lugar }}@endif
                </p>
            </div>
            {{-- Actions --}}
            <div style="display:flex;flex-direction:column;gap:.375rem;flex-shrink:0;">
                <a href="{{ route('admin.inscritos', $evento) }}" class="btn-primary" style="font-size:.75rem;padding:.4rem .75rem;justify-content:center;">Inscritos</a>
                <a href="{{ route('admin.encuestas.crear', $evento) }}" class="btn-ghost" style="font-size:.75rem;padding:.4rem .75rem;justify-content:center;">Encuesta</a>
            </div>
        </article>
        @empty
        <div style="background:rgba(168,224,99,.04);border:1px dashed rgba(168,224,99,.15);border-radius:.875rem;padding:2rem;text-align:center;color:#3d7055;font-size:.875rem;grid-column:1/-1;">
            No hay eventos registrados. <a href="{{ route('admin.eventos.crear') }}" style="color:#a8e063;font-weight:600;text-decoration:none;">Crear uno →</a>
        </div>
        @endforelse
    </div>
</section>
@endsection
