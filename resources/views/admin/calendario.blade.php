@extends('layouts.app')

@section('title', 'Calendario | Bienestar SENA')
@section('sidebar', true)

@section('content')
@php
$nombresMeses = [
    1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',
    7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre'
];

// Festivos Colombia — fijos y móviles aprox. para el año actual
// Se calculan los móviles con la regla "lunes siguiente" cuando aplica
$festivos = [];
$anioFestivos = $anio;

// Festivos fijos Colombia
$festivosFijos = [
    "$anioFestivos-01-01" => 'Año Nuevo',
    "$anioFestivos-05-01" => 'Día del Trabajo',
    "$anioFestivos-07-20" => 'Día de la Independencia',
    "$anioFestivos-08-07" => 'Batalla de Boyacá',
    "$anioFestivos-12-08" => 'Inmaculada Concepción',
    "$anioFestivos-12-25" => 'Navidad',
];

// Festivos "puente" (lunes siguiente si no cae lunes) — principales
$festivosPuente = [
    "$anioFestivos-01-06" => 'Reyes Magos',
    "$anioFestivos-03-19" => 'San José',
    "$anioFestivos-06-29" => 'San Pedro y San Pablo',
    "$anioFestivos-08-15" => 'Asunción de la Virgen',
    "$anioFestivos-10-12" => 'Día de la Raza',
    "$anioFestivos-11-01" => 'Todos los Santos',
    "$anioFestivos-11-11" => 'Independencia de Cartagena',
];

// Calcular semana santa basado en Pascua (algoritmo de Gauss)
$a = $anioFestivos % 19;
$b = intdiv($anioFestivos, 100);
$c = $anioFestivos % 100;
$d = intdiv($b, 4);
$e = $b % 4;
$f = intdiv($b + 8, 25);
$g = intdiv($b - $f + 1, 3);
$h = (19 * $a + $b - $d - $g + 15) % 30;
$i = intdiv($c, 4);
$k = $c % 4;
$l = (32 + 2 * $e + 2 * $i - $h - $k) % 7;
$m = intdiv($a + 11 * $h + 22 * $l, 451);
$mesPascua = intdiv($h + $l - 7 * $m + 114, 31);
$diaPascua = (($h + $l - 7 * $m + 114) % 31) + 1;
$pascua = \Carbon\Carbon::create($anioFestivos, $mesPascua, $diaPascua);

$festivosMobiles = [
    $pascua->copy()->subDays(3)->format('Y-m-d') => 'Jueves Santo',
    $pascua->copy()->subDays(2)->format('Y-m-d') => 'Viernes Santo',
    $pascua->copy()->addDays(43)->format('Y-m-d') => 'Ascensión de Jesús',
    $pascua->copy()->addDays(64)->format('Y-m-d') => 'Corpus Christi',
    $pascua->copy()->addDays(71)->format('Y-m-d') => 'Sagrado Corazón',
];

$festivos = array_merge($festivosFijos, $festivosPuente, $festivosMobiles);

// Obtener todos los eventos del año para JSON
$todosEventos = \App\Models\Evento::query()
    ->whereYear('fecha_evento', $anio)
    ->orderBy('fecha_evento')
    ->withCount('inscripciones')
    ->get(['id','nombre','descripcion','fecha_evento','lugar','cupo_maximo','estado','inscripciones_count']);
@endphp

<style>
/* ===== LAYOUT ===== */
.cal-wrap {
    display: grid;
    grid-template-columns: 1fr 340px;
    gap: 1.25rem;
    align-items: start;
}
@media (max-width: 960px) {
    .cal-wrap { grid-template-columns: 1fr; }
}

/* ===== CALENDAR ===== */
.cal-grid {
    background: var(--surface-card);
    border: 1px solid var(--border-dark);
    border-radius: 1rem;
    overflow: hidden;
}
.cal-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--border-dark);
    gap: .75rem; flex-wrap: wrap;
}
.cal-nav-btn {
    width: 34px; height: 34px;
    display: flex; align-items: center; justify-content: center;
    background: rgba(255,255,255,.05);
    border: 1px solid var(--border-dark);
    border-radius: .5rem;
    cursor: pointer; color: #a8e063;
    transition: background .15s;
    text-decoration: none;
}
.cal-nav-btn:hover { background: rgba(168,224,99,.1); }

.cal-weekdays {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    border-bottom: 1px solid var(--border-dark);
}
.cal-weekday {
    padding: .5rem .25rem;
    text-align: center;
    font-size: .68rem;
    font-weight: 700;
    letter-spacing: .07em;
    text-transform: uppercase;
    color: #5a9070;
    border-right: 1px solid rgba(30,64,48,.3);
}
.cal-weekday:last-child { border-right: none; }

.cal-body {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
}
.cal-cell {
    min-height: 90px;
    padding: .5rem .375rem;
    border-right: 1px solid rgba(30,64,48,.3);
    border-bottom: 1px solid rgba(30,64,48,.3);
    vertical-align: top;
    position: relative;
    transition: background .12s;
    cursor: default;
}
.cal-cell:nth-child(7n) { border-right: none; }
.cal-cell.empty { background: rgba(0,0,0,.2); }
.cal-cell.today { background: rgba(168,224,99,.04); }
.cal-cell.festivo { background: rgba(251,191,36,.03); }
.cal-cell:not(.empty):hover { background: rgba(168,224,99,.05); }

.cal-day-num {
    display: inline-flex; align-items: center; justify-content: center;
    width: 26px; height: 26px;
    border-radius: 50%;
    font-size: .78rem; font-weight: 600;
    color: #7aaa90; line-height: 1;
}
.cal-day-num.today {
    background: linear-gradient(135deg, #a8e063, #7db840);
    color: #071a11; font-weight: 800;
}
.cal-day-num.festivo-num { color: #f0bf3a; }

.cal-event-pill {
    display: block;
    margin-top: .25rem;
    padding: .2rem .375rem;
    background: rgba(29,107,61,.5);
    border: 1px solid rgba(168,224,99,.2);
    border-radius: .3rem;
    font-size: .65rem;
    font-weight: 600;
    color: #a8e063;
    line-height: 1.3;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    cursor: pointer;
    transition: background .12s, border-color .12s;
}
.cal-event-pill:hover {
    background: rgba(29,107,61,.8);
    border-color: rgba(168,224,99,.5);
}
.cal-festivo-pill {
    display: block;
    margin-top: .25rem;
    padding: .2rem .375rem;
    background: rgba(251,191,36,.1);
    border: 1px solid rgba(251,191,36,.2);
    border-radius: .3rem;
    font-size: .63rem;
    font-weight: 600;
    color: #f0bf3a;
    line-height: 1.3;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.cal-more {
    font-size: .62rem; color: #5a9070; font-weight: 600;
    margin-top: .2rem; cursor: pointer;
    transition: color .12s;
}
.cal-more:hover { color: #a8e063; }

/* ===== PANEL LATERAL ===== */
.side-panel {
    display: flex; flex-direction: column; gap: 1rem;
    position: sticky; top: 1.5rem;
}
.side-card {
    background: var(--surface-card);
    border: 1px solid var(--border-dark);
    border-radius: 1rem;
    overflow: hidden;
}
.side-card-hd {
    padding: .875rem 1.125rem;
    border-bottom: 1px solid var(--border-dark);
    display: flex; align-items: center; justify-content: space-between; gap: .5rem;
}
.side-card-body { padding: .875rem 1.125rem; }

/* Search */
.ev-search {
    width: 100%;
    background: rgba(0,0,0,.25);
    border: 1px solid var(--border-dark);
    border-radius: .625rem;
    padding: .6rem .875rem .6rem 2.25rem;
    color: #e8f5ee;
    font-family: 'Inter', sans-serif;
    font-size: .8125rem;
    outline: none;
    transition: border-color .15s, box-shadow .15s;
}
.ev-search:focus { border-color: #4ec483; box-shadow: 0 0 0 3px rgba(78,196,131,.1); }
.ev-search::placeholder { color: #3d7055; }

/* Event list item */
.ev-list-item {
    display: flex; gap: .75rem; align-items: flex-start;
    padding: .625rem .75rem;
    border-radius: .625rem;
    cursor: pointer;
    transition: background .12s;
    border: 1px solid transparent;
}
.ev-list-item:hover {
    background: rgba(168,224,99,.06);
    border-color: rgba(168,224,99,.12);
}
.ev-list-date {
    flex-shrink: 0;
    text-align: center;
    background: rgba(168,224,99,.1);
    border: 1px solid rgba(168,224,99,.15);
    border-radius: .5rem;
    padding: .35rem .5rem;
    min-width: 40px;
}

/* Pagination */
.pag-btn {
    display: inline-flex; align-items: center; justify-content: center;
    width: 30px; height: 30px;
    border-radius: .5rem;
    background: rgba(255,255,255,.05);
    border: 1px solid var(--border-dark);
    color: #7aaa90; font-size: .8125rem; font-weight: 600;
    cursor: pointer; transition: background .12s, color .12s, border-color .12s;
}
.pag-btn:hover, .pag-btn.active {
    background: rgba(168,224,99,.12);
    border-color: rgba(168,224,99,.3);
    color: #a8e063;
}
.pag-btn:disabled { opacity: .35; cursor: not-allowed; }

/* ===== MODAL ===== */
.modal-overlay {
    position: fixed; inset: 0; z-index: 100;
    background: rgba(7,26,17,.75);
    backdrop-filter: blur(6px);
    display: flex; align-items: center; justify-content: center;
    padding: 1rem;
    opacity: 0; pointer-events: none;
    transition: opacity .2s;
}
.modal-overlay.open { opacity: 1; pointer-events: all; }
.modal-box {
    background: #142818;
    border: 1px solid rgba(168,224,99,.15);
    border-radius: 1.125rem;
    width: 100%; max-width: 520px;
    box-shadow: 0 24px 64px rgba(0,0,0,.5);
    transform: translateY(16px) scale(.97);
    transition: transform .25s, opacity .25s;
    overflow: hidden;
}
.modal-overlay.open .modal-box { transform: translateY(0) scale(1); }
.modal-header {
    background: linear-gradient(135deg, #0d2b1d, #103c2c);
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid rgba(168,224,99,.1);
}
.modal-body { padding: 1.375rem 1.5rem; }
.modal-footer {
    padding: 1rem 1.5rem;
    border-top: 1px solid rgba(30,64,48,.5);
    display: flex; justify-content: flex-end; gap: .625rem;
}
.modal-detail-row {
    display: flex; align-items: flex-start; gap: .75rem;
    padding: .5rem 0;
    border-bottom: 1px solid rgba(30,64,48,.4);
    font-size: .875rem;
}
.modal-detail-row:last-child { border-bottom: none; }
.modal-detail-icon {
    flex-shrink: 0; width: 30px; height: 30px;
    border-radius: .5rem;
    background: rgba(168,224,99,.08);
    display: flex; align-items: center; justify-content: center;
}
</style>

{{-- Page header --}}
<div style="margin-bottom:1.5rem;" class="animate-fade-up">
    <span class="eyebrow" style="color:#5a9070;">Planificación anual</span>
    <h1 style="margin-top:.375rem;font-size:1.75rem;font-weight:800;color:#e8f5ee;letter-spacing:-.02em;">Calendario de bienestar</h1>
    <p style="margin-top:.25rem;font-size:.875rem;color:#5a9070;">
        {{ $nombresMeses[$mes] }} {{ $anio }} — eventos y festivos nacionales de Colombia.
    </p>
</div>

<div class="cal-wrap animate-fade-up-2">

    {{-- ===================== CALENDARIO IZQUIERDA ===================== --}}
    <div>
        {{-- Month nav --}}
        <div style="background:var(--surface-card);border:1px solid var(--border-dark);border-radius:1rem;overflow:hidden;">
            <div class="cal-header">
                <div style="display:flex;align-items:center;gap:.625rem;">
                    {{-- Prev month --}}
                    @php
                        $prevMes  = $mes === 1  ? 12 : $mes - 1;
                        $prevAnio = $mes === 1  ? $anio - 1 : $anio;
                        $nextMes  = $mes === 12 ? 1  : $mes + 1;
                        $nextAnio = $mes === 12 ? $anio + 1 : $anio;
                    @endphp
                    <a href="{{ route('admin.calendario', ['mes' => $prevMes, 'anio' => $prevAnio]) }}" class="cal-nav-btn" title="Mes anterior">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M15 18l-6-6 6-6"/></svg>
                    </a>
                    <span style="font-size:1.0625rem;font-weight:800;color:#e8f5ee;min-width:160px;text-align:center;">
                        {{ $nombresMeses[$mes] }} {{ $anio }}
                    </span>
                    <a href="{{ route('admin.calendario', ['mes' => $nextMes, 'anio' => $nextAnio]) }}" class="cal-nav-btn" title="Mes siguiente">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
                    </a>
                </div>
                {{-- Go to today + year selector --}}
                <div style="display:flex;align-items:center;gap:.5rem;flex-wrap:wrap;">
                    <a href="{{ route('admin.calendario') }}" class="btn-ghost" style="padding:.4rem .75rem;font-size:.78rem;">Hoy</a>
                    <form method="GET" style="display:flex;gap:.375rem;">
                        <select name="mes" class="field" style="padding:.4rem .625rem;font-size:.78rem;width:auto;" onchange="this.form.submit()">
                            @foreach($nombresMeses as $n => $nombre)
                            <option value="{{ $n }}" @selected($mes === $n)>{{ $nombre }}</option>
                            @endforeach
                        </select>
                        <input name="anio" type="number" min="2020" max="2100" value="{{ $anio }}" class="field" style="width:76px;padding:.4rem .5rem;font-size:.78rem;" onchange="this.form.submit()">
                        <noscript><button type="submit" class="btn-primary" style="padding:.4rem .75rem;font-size:.78rem;">Ir</button></noscript>
                    </form>
                </div>
            </div>

            {{-- Leyenda --}}
            <div style="display:flex;align-items:center;gap:1rem;padding:.5rem 1.25rem;border-bottom:1px solid var(--border-dark);background:rgba(0,0,0,.15);flex-wrap:wrap;">
                <span style="display:flex;align-items:center;gap:.375rem;font-size:.72rem;color:#5a9070;">
                    <span style="width:12px;height:12px;background:linear-gradient(135deg,#a8e063,#7db840);border-radius:50%;display:inline-block;"></span>
                    Hoy
                </span>
                <span style="display:flex;align-items:center;gap:.375rem;font-size:.72rem;color:#5a9070;">
                    <span style="width:12px;height:6px;background:rgba(29,107,61,.5);border:1px solid rgba(168,224,99,.2);border-radius:2px;display:inline-block;"></span>
                    Evento
                </span>
                <span style="display:flex;align-items:center;gap:.375rem;font-size:.72rem;color:#5a9070;">
                    <span style="width:12px;height:6px;background:rgba(251,191,36,.1);border:1px solid rgba(251,191,36,.2);border-radius:2px;display:inline-block;"></span>
                    Festivo
                </span>
            </div>

            {{-- Weekdays header --}}
            <div class="cal-weekdays">
                @foreach(['Lun','Mar','Mié','Jue','Vie','Sáb','Dom'] as $dia)
                <div class="cal-weekday">{{ $dia }}</div>
                @endforeach
            </div>

            {{-- Calendar body --}}
            @php
                $hoy = now('America/Bogota')->toDateString();
                $startDow = $inicio->isoWeekday(); // 1=Mon … 7=Sun
                $daysInMonth = $fin->day;
                // Build cells array: nulls for empty + day numbers
                $cells = array_merge(array_fill(0, $startDow - 1, null), range(1, $daysInMonth));
                // Pad to complete last row
                while (count($cells) % 7 !== 0) $cells[] = null;
            @endphp

            <div class="cal-body">
                @foreach($cells as $dia)
                @if($dia === null)
                    <div class="cal-cell empty"></div>
                @else
                    @php
                        $fecha = $inicio->setDay($dia)->toDateString();
                        $esHoy = $fecha === $hoy;
                        $festivoNombre = $festivos[$fecha] ?? null;
                        $eventosDelDia = $eventos->get($fecha, collect());
                    @endphp
                    <div class="cal-cell {{ $esHoy ? 'today' : '' }} {{ $festivoNombre ? 'festivo' : '' }}">
                        <span class="cal-day-num {{ $esHoy ? 'today' : ($festivoNombre ? 'festivo-num' : '') }}">{{ $dia }}</span>

                        @if($festivoNombre)
                        <span class="cal-festivo-pill" title="{{ $festivoNombre }}">🇨🇴 {{ $festivoNombre }}</span>
                        @endif

                        @foreach($eventosDelDia->take(2) as $evento)
                        <span
                            class="cal-event-pill"
                            onclick="abrirModal({{ $evento->id }})"
                            title="{{ $evento->nombre }}"
                        >{{ $evento->nombre }}</span>
                        @endforeach

                        @if($eventosDelDia->count() > 2)
                        <span class="cal-more" onclick="abrirPanelDia('{{ $fecha }}')">+{{ $eventosDelDia->count() - 2 }} más</span>
                        @endif
                    </div>
                @endif
                @endforeach
            </div>
        </div>

        {{-- Mini stats --}}
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:.75rem;margin-top:.875rem;">
            <div style="background:var(--surface-card);border:1px solid var(--border-dark);border-radius:.875rem;padding:.875rem 1rem;text-align:center;">
                <span style="font-size:1.5rem;font-weight:800;color:#a8e063;display:block;">{{ $eventos->flatten()->count() }}</span>
                <span style="font-size:.72rem;color:#5a9070;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Eventos este mes</span>
            </div>
            <div style="background:var(--surface-card);border:1px solid var(--border-dark);border-radius:.875rem;padding:.875rem 1rem;text-align:center;">
                <span style="font-size:1.5rem;font-weight:800;color:#f0bf3a;display:block;">{{ count(array_filter($festivos, fn($k) => str_starts_with($k, "$anio-" . str_pad($mes, 2, '0', STR_PAD_LEFT)), ARRAY_FILTER_USE_KEY)) }}</span>
                <span style="font-size:.72rem;color:#5a9070;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Festivos este mes</span>
            </div>
            <div style="background:var(--surface-card);border:1px solid var(--border-dark);border-radius:.875rem;padding:.875rem 1rem;text-align:center;">
                <span style="font-size:1.5rem;font-weight:800;color:#e8f5ee;display:block;">{{ $todosEventos->count() }}</span>
                <span style="font-size:.72rem;color:#5a9070;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Eventos en {{ $anio }}</span>
            </div>
        </div>
    </div>

    {{-- ===================== PANEL LATERAL ===================== --}}
    <div class="side-panel">

        {{-- Próximos eventos con buscador y paginación --}}
        <div class="side-card">
            <div class="side-card-hd">
                <div>
                    <p style="font-size:.8rem;font-weight:700;color:#e8f5ee;">Todos los eventos</p>
                    <p style="font-size:.72rem;color:#5a9070;margin-top:.1rem;">{{ $anio }} · <span id="ev-count">{{ $todosEventos->count() }}</span> registrados</p>
                </div>
                <a href="{{ route('admin.eventos.crear') }}" class="btn-primary" style="padding:.4rem .75rem;font-size:.75rem;white-space:nowrap;">
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                    Nuevo
                </a>
            </div>
            <div style="padding:.75rem .875rem;border-bottom:1px solid var(--border-dark);position:relative;">
                <svg style="position:absolute;left:1.5rem;top:50%;transform:translateY(-50%);pointer-events:none;" width="14" height="14" fill="none" stroke="#3d7055" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                <input
                    id="ev-search"
                    type="search"
                    placeholder="Buscar evento..."
                    class="ev-search"
                    oninput="filtrarEventos(this.value)"
                >
            </div>
            <div id="ev-list" style="padding:.5rem .625rem;max-height:380px;overflow-y:auto;"></div>
            {{-- Pagination --}}
            <div style="padding:.625rem .875rem;border-top:1px solid var(--border-dark);display:flex;align-items:center;justify-content:space-between;gap:.5rem;">
                <span id="pag-info" style="font-size:.72rem;color:#5a9070;"></span>
                <div style="display:flex;gap:.25rem;" id="pag-controls"></div>
            </div>
        </div>

        {{-- Festivos del mes --}}
        <div class="side-card">
            <div class="side-card-hd">
                <p style="font-size:.8rem;font-weight:700;color:#e8f5ee;">Festivos en {{ $nombresMeses[$mes] }}</p>
            </div>
            <div style="padding:.5rem .625rem;">
                @php
                    $mesStr = str_pad($mes, 2, '0', STR_PAD_LEFT);
                    $festivosMes = array_filter($festivos, fn($k) => str_starts_with($k, "$anio-$mesStr"), ARRAY_FILTER_USE_KEY);
                    ksort($festivosMes);
                @endphp
                @forelse($festivosMes as $fecha => $nombre)
                @php($carbon = \Carbon\Carbon::parse($fecha))
                <div style="display:flex;align-items:center;gap:.625rem;padding:.5rem .5rem;border-radius:.5rem;border-bottom:1px solid rgba(30,64,48,.3);">
                    <span style="flex-shrink:0;width:36px;height:36px;background:rgba(251,191,36,.1);border:1px solid rgba(251,191,36,.18);border-radius:.5rem;display:flex;align-items:center;justify-content:center;font-size:.8rem;font-weight:800;color:#f0bf3a;">{{ $carbon->day }}</span>
                    <div>
                        <span style="font-size:.8125rem;font-weight:600;color:#e8f5ee;display:block;">{{ $nombre }}</span>
                        <span style="font-size:.72rem;color:#5a9070;">{{ $carbon->translatedFormat('l') }}</span>
                    </div>
                </div>
                @empty
                <p style="text-align:center;color:#3d7055;font-size:.8rem;padding:1rem;">Sin festivos este mes.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- ===================== MODAL DE EVENTO ===================== --}}
<div class="modal-overlay" id="modal-overlay" onclick="if(event.target===this)cerrarModal()">
    <div class="modal-box">
        <div class="modal-header">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;">
                <div>
                    <span class="eyebrow" style="color:#a8e063;" id="modal-eyebrow">Evento</span>
                    <h2 id="modal-nombre" style="margin-top:.375rem;font-size:1.125rem;font-weight:800;color:#e8f5ee;line-height:1.3;"></h2>
                </div>
                <button onclick="cerrarModal()" style="flex-shrink:0;width:30px;height:30px;background:rgba(255,255,255,.07);border:1px solid var(--border-dark);border-radius:.5rem;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#5a9070;font-size:1rem;transition:background .12s;" onmouseover="this.style.background='rgba(255,255,255,.12)'" onmouseout="this.style.background='rgba(255,255,255,.07)'">&times;</button>
            </div>
        </div>
        <div class="modal-body">
            <div id="modal-descripcion" style="font-size:.875rem;color:#7aaa90;line-height:1.6;margin-bottom:1rem;padding-bottom:.875rem;border-bottom:1px solid rgba(30,64,48,.4);display:none;"></div>
            <div id="modal-details" style="display:flex;flex-direction:column;gap:.125rem;"></div>
        </div>
        <div class="modal-footer">
            <a id="modal-link-inscritos" href="#" class="btn-ghost" style="font-size:.8rem;padding:.5rem .875rem;">Ver inscritos</a>
            <a id="modal-link-encuesta" href="#" class="btn-primary" style="font-size:.8rem;padding:.5rem .875rem;">Crear encuesta</a>
        </div>
    </div>
</div>

{{-- Data JSON --}}
<script>
const EVENTOS_DATA = @json($todosEventos->keyBy('id'));
const ROUTES = {
    inscritos: "{{ rtrim(route('admin.inscritos', ['evento' => '__ID__']), '/') }}",
    encuesta:  "{{ rtrim(route('admin.encuestas.crear', ['evento' => '__ID__']), '/') }}"
};

// ====== MODAL ======
function abrirModal(id) {
    const ev = EVENTOS_DATA[id];
    if (!ev) return;

    const fecha = new Date(ev.fecha_evento + 'T12:00:00');
    const opts = { weekday:'long', year:'numeric', month:'long', day:'numeric' };
    const fechaStr = fecha.toLocaleDateString('es-CO', opts);

    document.getElementById('modal-eyebrow').textContent =
        ev.estado === 'PROGRAMADO' ? '📅 Programado' :
        ev.estado === 'CANCELADO'  ? '❌ Cancelado'  : '✅ ' + ev.estado;
    document.getElementById('modal-nombre').textContent = ev.nombre;

    const descEl = document.getElementById('modal-descripcion');
    if (ev.descripcion) {
        descEl.textContent = ev.descripcion;
        descEl.style.display = 'block';
    } else { descEl.style.display = 'none'; }

    const details = [];
    details.push(['📅', 'Fecha', fechaStr]);
    if (ev.lugar) details.push(['📍', 'Lugar', ev.lugar]);
    details.push(['👥', 'Inscritos', ev.inscripciones_count ?? 0]);
    if (ev.cupo_maximo) details.push(['🎟️', 'Cupo máximo', ev.cupo_maximo]);

    const detailsEl = document.getElementById('modal-details');
    detailsEl.innerHTML = details.map(([icon, label, val]) => `
        <div class="modal-detail-row">
            <div class="modal-detail-icon">${icon}</div>
            <div>
                <span style="font-size:.72rem;color:#5a9070;font-weight:600;text-transform:uppercase;letter-spacing:.05em;display:block;">${label}</span>
                <span style="font-size:.875rem;color:#e8f5ee;font-weight:500;">${val}</span>
            </div>
        </div>
    `).join('');

    document.getElementById('modal-link-inscritos').href = ROUTES.inscritos.replace('__ID__', id);
    document.getElementById('modal-link-encuesta').href  = ROUTES.encuesta.replace('__ID__', id);

    const overlay = document.getElementById('modal-overlay');
    overlay.classList.add('open');
    document.body.style.overflow = 'hidden';
}

function cerrarModal() {
    document.getElementById('modal-overlay').classList.remove('open');
    document.body.style.overflow = '';
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') cerrarModal(); });

// ====== LISTA CON BÚSQUEDA Y PAGINACIÓN ======
const ITEMS_PER_PAGE = 6;
let currentPage = 1;
let allEvents = Object.values(EVENTOS_DATA);
let filtered = [...allEvents];

function renderList() {
    const list = document.getElementById('ev-list');
    const start = (currentPage - 1) * ITEMS_PER_PAGE;
    const page = filtered.slice(start, start + ITEMS_PER_PAGE);

    document.getElementById('ev-count').textContent = filtered.length;

    if (page.length === 0) {
        list.innerHTML = '<p style="text-align:center;color:#3d7055;font-size:.8rem;padding:1.5rem;">Sin resultados.</p>';
        document.getElementById('pag-info').textContent = '';
        document.getElementById('pag-controls').innerHTML = '';
        return;
    }

    list.innerHTML = page.map(ev => {
        const d = new Date(ev.fecha_evento + 'T12:00:00');
        const dia  = String(d.getDate()).padStart(2, '0');
        const mes  = d.toLocaleDateString('es-CO', { month: 'short' }).replace('.','');
        const estado = ev.estado === 'PROGRAMADO'
            ? '<span style="font-size:.65rem;font-weight:700;color:#a8e063;background:rgba(168,224,99,.1);border:1px solid rgba(168,224,99,.15);padding:.1rem .4rem;border-radius:99px;">Programado</span>'
            : '<span style="font-size:.65rem;font-weight:700;color:#f87171;background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.15);padding:.1rem .4rem;border-radius:99px;">' + ev.estado + '</span>';
        return `
        <div class="ev-list-item" onclick="abrirModal(${ev.id})">
            <div class="ev-list-date">
                <span style="display:block;font-size:1.0625rem;font-weight:800;color:#a8e063;line-height:1;">${dia}</span>
                <span style="display:block;font-size:.62rem;font-weight:700;text-transform:uppercase;color:#5a9070;margin-top:.1rem;">${mes}</span>
            </div>
            <div style="flex:1;min-width:0;">
                <p style="font-size:.8125rem;font-weight:700;color:#e8f5ee;line-height:1.3;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${escHtml(ev.nombre)}</p>
                <div style="display:flex;align-items:center;gap:.375rem;margin-top:.25rem;flex-wrap:wrap;">
                    ${estado}
                    ${ev.lugar ? `<span style="font-size:.7rem;color:#5a9070;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">📍 ${escHtml(ev.lugar)}</span>` : ''}
                </div>
                <p style="font-size:.72rem;color:#3d7055;margin-top:.2rem;">👥 ${ev.inscripciones_count ?? 0} inscritos</p>
            </div>
        </div>`;
    }).join('');

    // Pagination
    const total = filtered.length;
    const pages = Math.ceil(total / ITEMS_PER_PAGE);
    document.getElementById('pag-info').textContent = `${start + 1}–${Math.min(start + ITEMS_PER_PAGE, total)} de ${total}`;

    const ctrl = document.getElementById('pag-controls');
    ctrl.innerHTML = '';

    const addBtn = (label, page, disabled, active) => {
        const btn = document.createElement('button');
        btn.className = 'pag-btn' + (active ? ' active' : '');
        btn.innerHTML = label;
        btn.disabled = disabled;
        btn.onclick = () => { currentPage = page; renderList(); };
        ctrl.appendChild(btn);
    };

    addBtn('‹', currentPage - 1, currentPage === 1, false);
    for (let p = 1; p <= pages; p++) {
        if (pages <= 7 || p === 1 || p === pages || Math.abs(p - currentPage) <= 1)
            addBtn(p, p, false, p === currentPage);
        else if (Math.abs(p - currentPage) === 2 && pages > 5) {
            const sp = document.createElement('span');
            sp.textContent = '…';
            sp.style.cssText = 'color:#3d7055;font-size:.8125rem;padding:0 .25rem;align-self:center;';
            ctrl.appendChild(sp);
        }
    }
    addBtn('›', currentPage + 1, currentPage === pages, false);
}

function filtrarEventos(q) {
    const term = q.trim().toLowerCase();
    filtered = term
        ? allEvents.filter(e =>
            e.nombre.toLowerCase().includes(term) ||
            (e.descripcion || '').toLowerCase().includes(term) ||
            (e.lugar || '').toLowerCase().includes(term) ||
            e.fecha_evento.includes(term))
        : [...allEvents];
    currentPage = 1;
    renderList();
}

function escHtml(s) {
    return String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
}

// Init
renderList();
</script>

@push('scripts')
<script>setTimeout(() => window.location.reload(), 120000);</script>
@endpush

@endsection
