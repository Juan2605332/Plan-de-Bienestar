@extends('layouts.app')
@section('title', 'Encuesta | Bienestar SENA')
@section('header-actions')<a href="{{ route('eventos.index') }}" class="rounded-lg bg-white/10 px-3 py-2 text-sm text-white">Volver a eventos</a>@endsection
@section('content')
<div class="mx-auto max-w-3xl"><div class="mb-6"><span class="eyebrow text-xs font-bold uppercase text-[#648273]">Tu opinión cuenta</span><h1 class="mt-2 text-3xl font-semibold">{{ $encuesta->titulo }}</h1><p class="mt-2 text-sm text-[#71847a]">{{ $encuesta->evento->nombre }} · {{ $encuesta->instrucciones }}</p></div>
<form class="space-y-5 rounded-2xl border border-[#d9e5dc] bg-white p-6 shadow-sm" method="POST" action="{{ route('encuestas.responder', $encuesta) }}">@csrf
@foreach($encuesta->preguntas as $pregunta)
@php($respuestaGuardada = $respuestas[$pregunta->id] ?? null)
@php($valorGuardado = old("respuestas.{$pregunta->id}", $respuestaGuardada?->respuesta_texto))
@php($seleccionMultiple = $pregunta->tipo_pregunta === 'MULTIPLE')
@php($valoresMultiples = $seleccionMultiple && $respuestaGuardada?->respuesta_texto ? json_decode($respuestaGuardada->respuesta_texto, true) : [])
<fieldset class="rounded-2xl border border-[#d9e5dc] bg-[#f8fbf8] p-5"><legend class="font-semibold">{{ $pregunta->enunciado }} @if($pregunta->es_obligatoria)<span class="text-[#b23c3c]">*</span>@endif</legend>
@if($pregunta->tipo_pregunta === 'ABIERTA')
<textarea class="mt-4 w-full rounded-xl border border-[#cddbd1] bg-white p-3 outline-none focus:border-[#23734d]" name="respuestas[{{ $pregunta->id }}]" rows="4" @required($pregunta->es_obligatoria)>{{ $valorGuardado }}</textarea>
@elseif($pregunta->tipo_pregunta === 'NUMERO' || $pregunta->tipo_pregunta === 'FECHA')
<input type="{{ $pregunta->tipo_pregunta === 'NUMERO' ? 'number' : 'date' }}" class="mt-4 w-full rounded-xl border border-[#cddbd1] bg-white p-3 outline-none focus:border-[#23734d]" name="respuestas[{{ $pregunta->id }}]" value="{{ $valorGuardado }}" @required($pregunta->es_obligatoria)>
@else
<div class="mt-4 space-y-3">@foreach($pregunta->opciones as $opcion)<label class="flex items-center gap-3 rounded-xl border border-[#d9e5dc] bg-white p-3 text-sm hover:border-[#9dbbaa]"><input type="{{ $seleccionMultiple ? 'checkbox' : 'radio' }}" name="respuestas[{{ $pregunta->id }}]{{ $seleccionMultiple ? '[]' : '' }}" value="{{ $opcion->id }}" @checked($seleccionMultiple ? in_array($opcion->id, old("respuestas.{$pregunta->id}", $valoresMultiples), true) : old("respuestas.{$pregunta->id}", $respuestaGuardada?->opcion_id) == $opcion->id) @required($pregunta->es_obligatoria) class="h-4 w-4 text-[#1d6b3d]"><span>{{ $opcion->texto_opcion }}</span></label>@endforeach</div>
@endif</fieldset>
@endforeach
<div class="flex justify-end gap-3 border-t border-[#edf2ee] pt-5"><a href="{{ route('eventos.index') }}" class="rounded-xl bg-[#f0f5ef] px-5 py-3 text-sm font-semibold text-[#426b53]">Cancelar</a><button class="rounded-xl bg-[#1d6b3d] px-5 py-3 text-sm font-semibold text-white hover:bg-[#155630]">Enviar respuestas</button></div></form></div>
@endsection
