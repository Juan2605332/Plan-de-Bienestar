@extends('layouts.app')
@section('title', 'Encuesta | Bienestar SENA')
@section('header-actions')
<a href="{{ route('eventos.index') }}" style="display:inline-flex;align-items:center;gap:.5rem;padding:.5rem .875rem;border-radius:.5rem;font-size:.8125rem;font-weight:500;color:rgba(232,245,238,.7);border:1px solid rgba(255,255,255,.1);text-decoration:none;transition:background .15s;" onmouseover="this.style.background='rgba(255,255,255,.08)'" onmouseout="this.style.background='transparent'">
    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
    Volver a eventos
</a>
@endsection
@section('content')
<style>
    .survey-question {
        background: #fff;
        border: 1px solid #dceae0;
        border-radius: 1rem;
        overflow: hidden;
        transition: border-color .15s;
    }
    .survey-question:hover { border-color: #a8e063; }
    .survey-q-header {
        background: linear-gradient(135deg, #f2f7f4, #eaf5ec);
        border-bottom: 1px solid #dceae0;
        padding: .875rem 1.25rem;
        display: flex; align-items: flex-start; gap: .75rem;
    }
    .q-number {
        flex-shrink: 0;
        width: 28px; height: 28px;
        background: linear-gradient(135deg, #103c2c, #1d6b3d);
        border-radius: .5rem;
        display: flex; align-items: center; justify-content: center;
        font-size: .75rem; font-weight: 800; color: #a8e063;
    }
    .survey-q-body { padding: 1.125rem 1.25rem; }
    .option-label {
        display: flex; align-items: center; gap: .75rem;
        padding: .75rem 1rem;
        background: #f8fbf8;
        border: 1px solid #dceae0;
        border-radius: .625rem;
        cursor: pointer;
        transition: border-color .15s, background .15s;
        font-size: .875rem; color: #2a4a36;
    }
    .option-label:hover { border-color: #23a05a; background: #eef8f1; }
    .option-label input:checked ~ span { color: #1d6b3d; font-weight: 600; }
</style>

<div style="max-width:700px;">
    {{-- Header --}}
    <div style="margin-bottom:2rem;">
        <span style="font-size:.7rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:#8aab98;">Tu opinión cuenta</span>
        <h1 style="margin-top:.375rem;font-size:1.6rem;font-weight:800;color:#1a3828;letter-spacing:-.02em;">{{ $encuesta->titulo }}</h1>
        <p style="margin-top:.375rem;font-size:.875rem;color:#5a7a65;line-height:1.5;">
            <strong style="color:#1a3828;">{{ $encuesta->evento->nombre }}</strong>
            @if($encuesta->instrucciones) — {{ $encuesta->instrucciones }}@endif
        </p>
    </div>

    <form method="POST" action="{{ route('encuestas.responder', $encuesta) }}" style="display:flex;flex-direction:column;gap:1rem;">
        @csrf

        @foreach($encuesta->preguntas as $qi => $pregunta)
        @php($respuestaGuardada = $respuestas[$pregunta->id] ?? null)
        @php($valorGuardado = old("respuestas.{$pregunta->id}", $respuestaGuardada?->respuesta_texto))
        @php($seleccionMultiple = $pregunta->tipo_pregunta === 'MULTIPLE')
        @php($valoresMultiples = $seleccionMultiple && $respuestaGuardada?->respuesta_texto ? json_decode($respuestaGuardada->respuesta_texto, true) : [])

        <div class="survey-question" style="animation: fadeUp .4s {{ $qi * 0.06 }}s ease both;">
            <div class="survey-q-header">
                <span class="q-number">{{ $qi + 1 }}</span>
                <div>
                    <p style="font-size:.9rem;font-weight:700;color:#1a3828;line-height:1.35;">
                        {{ $pregunta->enunciado }}
                        @if($pregunta->es_obligatoria)<span style="color:#e05353;margin-left:.25rem;">*</span>@endif
                    </p>
                </div>
            </div>
            <div class="survey-q-body">
                @if($pregunta->tipo_pregunta === 'ABIERTA')
                <textarea
                    class="field-public"
                    style="background:#fff;border:1px solid #dceae0;border-radius:.75rem;padding:.875rem 1rem;color:#1a3828;font-family:'Inter',sans-serif;font-size:.875rem;outline:none;transition:border-color .15s,box-shadow .15s;resize:vertical;width:100%;"
                    name="respuestas[{{ $pregunta->id }}]"
                    rows="4"
                    placeholder="Escribe tu respuesta aquí..."
                    @required($pregunta->es_obligatoria)
                >{{ $valorGuardado }}</textarea>

                @elseif($pregunta->tipo_pregunta === 'NUMERO' || $pregunta->tipo_pregunta === 'FECHA')
                <input
                    type="{{ $pregunta->tipo_pregunta === 'NUMERO' ? 'number' : 'date' }}"
                    style="background:#fff;border:1px solid #dceae0;border-radius:.75rem;padding:.875rem 1rem;color:#1a3828;font-family:'Inter',sans-serif;font-size:.875rem;outline:none;transition:border-color .15s,box-shadow .15s;width:100%;max-width:280px;"
                    name="respuestas[{{ $pregunta->id }}]"
                    value="{{ $valorGuardado }}"
                    @required($pregunta->es_obligatoria)
                >

                @else
                <div style="display:flex;flex-direction:column;gap:.5rem;">
                    @foreach($pregunta->opciones as $opcion)
                    <label class="option-label">
                        <input
                            type="{{ $seleccionMultiple ? 'checkbox' : 'radio' }}"
                            name="respuestas[{{ $pregunta->id }}]{{ $seleccionMultiple ? '[]' : '' }}"
                            value="{{ $opcion->id }}"
                            @checked($seleccionMultiple
                                ? in_array($opcion->id, old("respuestas.{$pregunta->id}", $valoresMultiples), true)
                                : old("respuestas.{$pregunta->id}", $respuestaGuardada?->opcion_id) == $opcion->id)
                            @required($pregunta->es_obligatoria)
                            style="width:16px;height:16px;accent-color:#1d6b3d;flex-shrink:0;"
                        >
                        <span>{{ $opcion->texto_opcion }}</span>
                    </label>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
        @endforeach

        <div style="display:flex;justify-content:flex-end;gap:.75rem;padding-top:.5rem;">
            <a href="{{ route('eventos.index') }}" style="display:inline-flex;align-items:center;padding:.7rem 1.25rem;background:#f2f7f4;border:1px solid #dceae0;border-radius:.625rem;font-size:.875rem;font-weight:600;color:#426b53;text-decoration:none;transition:background .15s;" onmouseover="this.style.background='#e4eee5'" onmouseout="this.style.background='#f2f7f4'">Cancelar</a>
            <button type="submit" class="btn-public">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path d="M22 2 11 13"/><path d="M22 2 15 22 11 13 2 9l20-7z"/></svg>
                Enviar respuestas
            </button>
        </div>
    </form>
</div>

@push('head')
<style>
    @keyframes fadeUp { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }
    .btn-public { display:inline-flex;align-items:center;gap:.5rem;background:#1d6b3d;color:#fff;font-weight:600;font-size:.875rem;padding:.7rem 1.4rem;border-radius:.625rem;border:none;cursor:pointer;transition:background .15s,box-shadow .15s;font-family:'Inter',sans-serif; }
    .btn-public:hover { background:#155630;box-shadow:0 4px 16px rgba(29,107,61,.3); }
</style>
@endpush
@endsection
