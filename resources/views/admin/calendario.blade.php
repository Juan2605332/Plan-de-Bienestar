@extends('layouts.app')

@section('title', 'Calendario | Bienestar SENA')

@section('header-actions')
<a href="{{ route('admin.dashboard') }}" class="rounded-lg px-3 py-2 text-sm text-[#d8e9dc] transition hover:bg-white/10">Panel</a>
<form action="{{ route('salir') }}" method="POST">@csrf<button class="rounded-lg bg-white/10 px-3 py-2 text-sm text-white transition hover:bg-white/20">Cerrar sesión</button></form>
@endsection

@section('content')
@php($nombresMeses = [1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'])
<div class="flex flex-wrap items-end justify-between gap-5"><div><span class="eyebrow text-xs font-bold uppercase text-[#648273]">Planificación anual</span><h1 class="mt-2 text-3xl font-semibold tracking-tight">Calendario de bienestar</h1><p class="mt-2 text-sm text-[#71847a]">Eventos y cumpleaños actualizados desde la agenda institucional.</p></div><form method="GET" class="flex items-center gap-2 rounded-2xl border border-[#d9e5dc] bg-white p-2 shadow-sm"><select name="mes" class="rounded-xl border-0 bg-[#f4f7f3] px-3 py-2 text-sm font-medium outline-none">@foreach($nombresMeses as $numeroMes => $nombreMes)<option value="{{ $numeroMes }}" @selected($mes === $numeroMes)>{{ $nombreMes }}</option>@endforeach</select><input name="anio" type="number" min="2020" max="2100" value="{{ $anio }}" class="w-20 rounded-xl border-0 bg-[#f4f7f3] px-3 py-2 text-sm outline-none"><button class="rounded-xl bg-[#1d6b3d] px-4 py-2 text-sm font-semibold text-white hover:bg-[#155630]">Actualizar</button></form></div>
<div class="mt-8 overflow-hidden rounded-2xl border border-[#d9e5dc] bg-[#d9e5dc] shadow-sm"><div class="grid grid-cols-7 gap-px">@foreach(['Lun','Mar','Mie','Jue','Vie','Sab','Dom'] as $diaSemana)<div class="bg-[#eaf1eb] p-3 text-center text-xs font-bold uppercase tracking-wide text-[#648273]">{{ $diaSemana }}</div>@endforeach @for($i = 1; $i < $inicio->isoWeekday(); $i++)<div class="min-h-28 bg-[#f8fbf8]"></div>@endfor @for($dia = 1; $dia <= $fin->day; $dia++)@php($fecha = $inicio->setDay($dia)->toDateString())<div class="min-h-28 bg-white p-3 align-top"><strong class="flex h-7 w-7 items-center justify-center rounded-full text-sm {{ $fecha === now('America/Bogota')->toDateString() ? 'bg-[#b8d66f] text-[#103c2c]' : 'text-[#426b53]' }}">{{ $dia }}</strong>@foreach($eventos->get($fecha, []) as $evento)<div class="mt-2 rounded-lg border border-[#b9d9c2] bg-[#eaf6ed] p-2 text-xs font-medium leading-4 text-[#1d6b3d]">{{ $evento->nombre }}</div>@endforeach</div>@endfor</div></div>
@endsection

@push('scripts')<script>setTimeout(() => window.location.reload(), 60000);</script>@endpush
