@extends('layouts.app')
@section('title', 'Nueva encuesta | Bienestar SENA')
@section('sidebar', true)
@section('content')
<style>
    .pregunta-card {
        background: var(--surface-card);
        border: 1px solid var(--border-dark);
        border-radius: .875rem;
        overflow: hidden;
        transition: border-color .15s, box-shadow .15s;
    }
    .pregunta-card:hover { border-color: rgba(168,224,99,.2); }
    .pregunta-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: .75rem 1.125rem;
        background: rgba(0,0,0,.25);
        border-bottom: 1px solid var(--border-dark);
        gap: .75rem;
    }
    .pregunta-body { padding: 1.125rem; }
    .tipo-badge {
        font-size: .65rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
        padding: .175rem .5rem;
        border-radius: 99px;
        background: rgba(168,224,99,.1);
        color: #a8e063;
        border: 1px solid rgba(168,224,99,.2);
    }
</style>

<div style="max-width:820px;" class="animate-fade-up">
    <div style="margin-bottom:1.5rem;">
        <span class="eyebrow" style="color:#5a9070;">Medición de impacto</span>
        <h1 style="margin-top:.375rem;font-size:1.6rem;font-weight:800;color:#e8f5ee;letter-spacing:-.02em;">Crear encuesta</h1>
        <p style="margin-top:.25rem;font-size:.875rem;color:#5a9070;">{{ $evento->nombre }}</p>
    </div>

    <form method="POST" action="{{ route('admin.encuestas.guardar', $evento) }}" style="display:flex;flex-direction:column;gap:1rem;">
        @csrf

        {{-- Información encuesta --}}
        <div style="background:var(--surface-card);border:1px solid var(--border-dark);border-radius:1rem;padding:1.5rem;" class="animate-fade-up-2">
            <p style="font-size:.7rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:#5a9070;margin-bottom:1.125rem;display:flex;align-items:center;gap:.625rem;">
                Configuración general
                <span style="flex:1;height:1px;background:var(--border-dark);display:block;"></span>
            </p>
            <div style="display:grid;gap:1rem;">
                <label style="display:flex;flex-direction:column;gap:.375rem;font-size:.8rem;font-weight:600;color:#7aaa90;">
                    Título de la encuesta
                    <input class="field" name="titulo" value="{{ old('titulo') }}" required placeholder="Ej. Evaluación de satisfacción">
                </label>
                <label style="display:flex;flex-direction:column;gap:.375rem;font-size:.8rem;font-weight:600;color:#7aaa90;">
                    Instrucciones <span style="font-weight:400;color:#3d7055;">(opcional)</span>
                    <textarea class="field" name="instrucciones" rows="2" placeholder="Indica cómo deben completar la encuesta...">{{ old('instrucciones') }}</textarea>
                </label>
                <label style="display:flex;flex-direction:column;gap:.375rem;font-size:.8rem;font-weight:600;color:#7aaa90;max-width:280px;">
                    Fecha límite <span style="font-weight:400;color:#3d7055;">(opcional)</span>
                    <input class="field" name="fecha_limite_respuesta" type="datetime-local">
                </label>
            </div>
        </div>

        {{-- Preguntas --}}
        <div class="animate-fade-up-3">
            <div style="display:flex;align-items:center;justify-content:space-between;gap:1rem;margin-bottom:1rem;">
                <div>
                    <h2 style="font-size:1rem;font-weight:700;color:#e8f5ee;">Preguntas</h2>
                    <p style="font-size:.78rem;color:#5a9070;margin-top:.125rem;">Configura el tipo de respuesta esperada.</p>
                </div>
                <button type="button" id="agregar-pregunta" class="btn-primary" style="white-space:nowrap;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                    Agregar pregunta
                </button>
            </div>
            <div id="preguntas-lista" style="display:flex;flex-direction:column;gap:.75rem;"></div>
            <div id="preguntas-error" style="display:none;background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.2);color:#f87171;border-radius:.75rem;padding:.75rem 1rem;font-size:.8125rem;margin-top:.75rem;">
                Agrega al menos una pregunta completa antes de continuar.
            </div>
        </div>

        {{-- Actions --}}
        <div style="display:flex;justify-content:flex-end;gap:.75rem;padding-top:.5rem;" class="animate-fade-up-4">
            <a href="{{ route('admin.dashboard') }}" class="btn-ghost">Cancelar</a>
            <button type="submit" class="btn-primary">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Crear encuesta
            </button>
        </div>
    </form>
</div>

<template id="pregunta-template">
    <div class="pregunta-card">
        <div class="pregunta-header">
            <div style="display:flex;align-items:center;gap:.625rem;">
                <span style="width:20px;height:20px;display:flex;flex-direction:column;justify-content:center;gap:3px;cursor:grab;opacity:.4;flex-shrink:0;">
                    <span style="height:2px;background:#e8f5ee;border-radius:1px;"></span>
                    <span style="height:2px;background:#e8f5ee;border-radius:1px;"></span>
                    <span style="height:2px;background:#e8f5ee;border-radius:1px;"></span>
                </span>
                <span class="pregunta-titulo" style="font-size:.75rem;font-weight:700;color:#e8f5ee;text-transform:uppercase;letter-spacing:.06em;">Pregunta</span>
                <span class="tipo-badge tipo-label">Texto libre</span>
            </div>
            <div style="display:flex;gap:.5rem;">
                <button type="button" data-duplicar class="btn-ghost" style="padding:.3rem .625rem;font-size:.72rem;">Duplicar</button>
                <button type="button" data-eliminar style="background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.15);color:#f87171;font-size:.72rem;padding:.3rem .625rem;border-radius:.5rem;cursor:pointer;transition:background .12s;" onmouseover="this.style.background='rgba(239,68,68,.2)'" onmouseout="this.style.background='rgba(239,68,68,.1)'">Eliminar</button>
            </div>
        </div>
        <div class="pregunta-body" style="display:flex;flex-direction:column;gap:.875rem;">
            <label style="display:flex;flex-direction:column;gap:.35rem;font-size:.8rem;font-weight:600;color:#7aaa90;">
                Enunciado de la pregunta
                <input data-enunciado class="field" required placeholder="¿Qué quieres preguntarle al funcionario?">
            </label>
            <div style="display:grid;grid-template-columns:1fr auto;gap:.875rem;align-items:end;">
                <label style="display:flex;flex-direction:column;gap:.35rem;font-size:.8rem;font-weight:600;color:#7aaa90;">
                    Tipo de respuesta
                    <select data-tipo class="field">
                        <option value="ABIERTA">Texto libre</option>
                        <option value="MULTIPLE_UNICA">Selección única</option>
                        <option value="MULTIPLE">Selección múltiple</option>
                        <option value="ESCALA_1_5">Escala de 1 a 5</option>
                        <option value="BOOLEANO">Sí / No</option>
                        <option value="NUMERO">Número</option>
                        <option value="FECHA">Fecha</option>
                        <option value="GENERO">Género</option>
                        <option value="RANGO_EDAD">Rango de edad</option>
                        <option value="HIJOS">¿Tiene hijos?</option>
                    </select>
                </label>
                <label style="display:flex;align-items:center;gap:.5rem;font-size:.8rem;color:#c4ddd0;cursor:pointer;padding-bottom:.625rem;white-space:nowrap;">
                    <input data-obligatoria type="checkbox" checked style="width:16px;height:16px;accent-color:#a8e063;"> Obligatoria
                </label>
            </div>

            {{-- Gender options --}}
            <div data-genero-wrap style="display:none;background:rgba(0,0,0,.2);border:1px solid var(--border-dark);border-radius:.75rem;padding:1rem;">
                <p style="font-size:.8rem;font-weight:600;color:#7aaa90;margin-bottom:.625rem;">Opciones de género</p>
                <div style="display:flex;flex-wrap:wrap;gap:.875rem;">
                    <label style="display:flex;align-items:center;gap:.5rem;font-size:.8125rem;color:#c4ddd0;cursor:pointer;"><input data-genero="FEMENINO" type="checkbox" style="accent-color:#a8e063;"> Mujer</label>
                    <label style="display:flex;align-items:center;gap:.5rem;font-size:.8125rem;color:#c4ddd0;cursor:pointer;"><input data-genero="MASCULINO" type="checkbox" style="accent-color:#a8e063;"> Hombre</label>
                    <label style="display:flex;align-items:center;gap:.5rem;font-size:.8125rem;color:#c4ddd0;cursor:pointer;"><input data-genero="OTRO" type="checkbox" style="accent-color:#a8e063;"> Otro</label>
                </div>
            </div>

            {{-- Age range --}}
            <div data-rango-wrap style="display:none;background:rgba(0,0,0,.2);border:1px solid var(--border-dark);border-radius:.75rem;padding:1rem;">
                <p style="font-size:.8rem;font-weight:600;color:#7aaa90;margin-bottom:.625rem;">Rango de edad</p>
                <div style="display:flex;gap:.625rem;flex-wrap:wrap;align-items:flex-end;">
                    <label style="display:flex;flex-direction:column;gap:.35rem;font-size:.75rem;color:#5a9070;">Desde<input data-rango-min type="number" min="0" max="120" class="field" style="width:90px;"></label>
                    <label style="display:flex;flex-direction:column;gap:.35rem;font-size:.75rem;color:#5a9070;">Hasta<input data-rango-max type="number" min="0" max="120" class="field" style="width:90px;"></label>
                    <button type="button" data-agregar-rango class="btn-primary" style="padding:.55rem 1rem;font-size:.78rem;">Agregar rango</button>
                </div>
                <div data-rangos-lista style="margin-top:.75rem;display:flex;flex-direction:column;gap:.375rem;"></div>
            </div>

            {{-- Options textarea --}}
            <label data-opciones-wrap style="display:flex;flex-direction:column;gap:.35rem;font-size:.8rem;font-weight:600;color:#7aaa90;">
                Opciones de respuesta
                <span style="font-weight:400;color:#3d7055;font-size:.75rem;">Para selección única, múltiple, sí/no. Una opción por línea.</span>
                <textarea data-opciones class="field" rows="4" placeholder="Nunca&#10;A veces&#10;Siempre"></textarea>
            </label>
            <input data-opciones-hidden type="hidden">
        </div>
    </div>
</template>

@endsection
@push('scripts')
<script>
const tipoLabels = { ABIERTA:'Texto libre', MULTIPLE_UNICA:'Selección única', MULTIPLE:'Selección múltiple', ESCALA_1_5:'Escala 1-5', BOOLEANO:'Sí/No', NUMERO:'Número', FECHA:'Fecha', GENERO:'Género', RANGO_EDAD:'Rango edad', HIJOS:'¿Hijos?' };
const listaPreguntas = document.getElementById('preguntas-lista');
const plantillaPregunta = document.getElementById('pregunta-template');
let indicePregunta = 0;
const presets = { ESCALA_1_5: ['1','2','3','4','5'], BOOLEANO: ['Sí','No'], HIJOS: ['Sí','No'] };

function actualizarOpciones(pregunta) {
    const tipo = pregunta.querySelector('[data-tipo]').value;
    const opciones = pregunta.querySelector('[data-opciones]');
    const genero = [...pregunta.querySelectorAll('[data-genero]:checked')].map(c => c.dataset.genero);
    pregunta.querySelector('.tipo-label').textContent = tipoLabels[tipo] || tipo;
    if (tipo === 'GENERO' && genero.length) opciones.value = genero.join('\n');
    if (presets[tipo] && !opciones.value.trim()) opciones.value = presets[tipo].join('\n');
    const showOpts = !['ABIERTA','NUMERO','FECHA','RANGO_EDAD','GENERO'].includes(tipo);
    pregunta.querySelector('[data-opciones-wrap]').style.display = showOpts ? 'flex' : 'none';
    pregunta.querySelector('[data-genero-wrap]').style.display = tipo === 'GENERO' ? 'block' : 'none';
    pregunta.querySelector('[data-rango-wrap]').style.display = tipo === 'RANGO_EDAD' ? 'block' : 'none';
    if (tipo === 'RANGO_EDAD') opciones.value = [...pregunta.querySelectorAll('[data-rango-label]')].map(r => r.dataset.rangoLabel).join('\n');
}

function agregarRango(pregunta) {
    const min = pregunta.querySelector('[data-rango-min]').value;
    const max = pregunta.querySelector('[data-rango-max]').value;
    if (!min || !max || Number(min) > Number(max)) return;
    const lista = pregunta.querySelector('[data-rangos-lista]');
    const rango = document.createElement('div');
    rango.dataset.rangoLabel = `${min} a ${max} años`;
    rango.style.cssText = 'display:flex;align-items:center;justify-content:space-between;background:rgba(168,224,99,.07);border:1px solid rgba(168,224,99,.15);border-radius:.5rem;padding:.375rem .75rem;font-size:.8rem;color:#a8e063;';
    rango.innerHTML = `<span>${min} a ${max} años</span><button type="button" data-quitar-rango style="background:none;border:none;color:#f87171;cursor:pointer;font-size:.75rem;font-weight:600;">Quitar</button>`;
    rango.querySelector('[data-quitar-rango]').addEventListener('click', () => { rango.remove(); actualizarOpciones(pregunta); });
    lista.appendChild(rango);
    pregunta.querySelector('[data-rango-min]').value = '';
    pregunta.querySelector('[data-rango-max]').value = '';
    actualizarOpciones(pregunta);
}

function renumerar() {
    listaPreguntas.querySelectorAll('.pregunta-titulo').forEach((el, i) => el.textContent = `Pregunta ${i + 1}`);
}

function agregarPregunta(datos = {}) {
    const pregunta = plantillaPregunta.content.cloneNode(true).firstElementChild;
    const indice = indicePregunta++;
    pregunta.querySelector('[data-enunciado]').name = `preguntas[${indice}][enunciado]`;
    pregunta.querySelector('[data-tipo]').name = `preguntas[${indice}][tipo_pregunta]`;
    pregunta.querySelector('[data-tipo]').value = datos.tipo || 'ABIERTA';
    pregunta.querySelector('[data-enunciado]').value = datos.enunciado || '';
    pregunta.querySelector('[data-obligatoria]').name = `preguntas[${indice}][es_obligatoria]`;
    pregunta.querySelector('[data-obligatoria]').value = '1';
    pregunta.querySelector('[data-opciones]').name = `preguntas[${indice}][opciones]`;
    pregunta.querySelector('[data-opciones]').value = datos.opciones || '';
    pregunta.querySelector('[data-tipo]').addEventListener('change', () => actualizarOpciones(pregunta));
    pregunta.querySelectorAll('[data-genero]').forEach(c => c.addEventListener('change', () => actualizarOpciones(pregunta)));
    pregunta.querySelector('[data-agregar-rango]').addEventListener('click', () => agregarRango(pregunta));
    pregunta.querySelector('[data-eliminar]').addEventListener('click', () => { pregunta.remove(); renumerar(); });
    pregunta.querySelector('[data-duplicar]').addEventListener('click', () => agregarPregunta({
        enunciado: pregunta.querySelector('[data-enunciado]').value,
        tipo: pregunta.querySelector('[data-tipo]').value,
        opciones: pregunta.querySelector('[data-opciones]').value
    }));
    listaPreguntas.appendChild(pregunta);
    actualizarOpciones(pregunta);
    renumerar();
    // Scroll to new question
    setTimeout(() => pregunta.scrollIntoView({ behavior: 'smooth', block: 'nearest' }), 50);
}

document.getElementById('agregar-pregunta').addEventListener('click', () => agregarPregunta());
document.querySelector('form').addEventListener('submit', e => {
    const err = document.getElementById('preguntas-error');
    if (!listaPreguntas.children.length || [...listaPreguntas.querySelectorAll('[data-enunciado]')].some(i => !i.value.trim())) {
        e.preventDefault();
        err.style.display = 'block';
        err.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
});
agregarPregunta();
</script>
@endpush
