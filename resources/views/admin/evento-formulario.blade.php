@extends('layouts.app')
@section('title', 'Nuevo evento | Bienestar SENA')
@section('sidebar', true)
@section('content')
<style>
    .form-section {
        background: var(--surface-card);
        border: 1px solid var(--border-dark);
        border-radius: 1rem;
        padding: 1.5rem;
        margin-bottom: 1rem;
    }
    .form-section-title {
        font-size: .7rem;
        font-weight: 700;
        letter-spacing: .1em;
        text-transform: uppercase;
        color: #5a9070;
        margin-bottom: 1.125rem;
        display: flex;
        align-items: center;
        gap: .625rem;
    }
    .form-section-title::after {
        content: '';
        flex: 1;
        height: 1px;
        background: var(--border-dark);
    }
    .form-label {
        display: flex;
        flex-direction: column;
        gap: .375rem;
        font-size: .8rem;
        font-weight: 600;
        color: #7aaa90;
    }
    .toggle-checkbox {
        appearance: none;
        width: 40px; height: 22px;
        background: rgba(255,255,255,.08);
        border-radius: 99px;
        border: 1px solid var(--border-dark);
        cursor: pointer;
        transition: background .2s;
        position: relative;
        flex-shrink: 0;
    }
    .toggle-checkbox::after {
        content: '';
        position: absolute;
        top: 2px; left: 2px;
        width: 16px; height: 16px;
        border-radius: 50%;
        background: #5a9070;
        transition: left .2s, background .2s;
    }
    .toggle-checkbox:checked {
        background: rgba(168,224,99,.2);
        border-color: rgba(168,224,99,.4);
    }
    .toggle-checkbox:checked::after {
        left: 20px;
        background: #a8e063;
    }
</style>

<div style="max-width:800px;" class="animate-fade-up">
    <div style="margin-bottom:1.5rem;">
        <span class="eyebrow" style="color:#5a9070;">Agenda institucional</span>
        <h1 style="margin-top:.375rem;font-size:1.6rem;font-weight:800;color:#e8f5ee;letter-spacing:-.02em;">Crear evento</h1>
        <p style="margin-top:.25rem;font-size:.875rem;color:#5a9070;">Diseña la actividad y define con precisión quién puede participar.</p>
    </div>

    @if($periodos->isEmpty())
    <div style="background:rgba(251,191,36,.07);border:1px solid rgba(251,191,36,.18);color:#f0bf3a;border-radius:.875rem;padding:1.25rem 1.5rem;font-size:.875rem;">
        <strong>Atención:</strong> Primero debes
        <a href="{{ route('admin.periodos.crear') }}" style="color:#f0bf3a;font-weight:700;">crear un período de inscripción</a>
        para poder añadir eventos.
    </div>
    @else
    <form method="POST" action="{{ route('admin.eventos.guardar') }}" style="display:flex;flex-direction:column;gap:1rem;">
        @csrf

        {{-- Información básica --}}
        <div class="form-section animate-fade-up-2">
            <p class="form-section-title">Información básica</p>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                <label class="form-label">
                    Período de inscripción
                    <select name="periodo_id" class="field">
                        @foreach($periodos as $periodo)
                        <option value="{{ $periodo->id }}">{{ $periodo->nombre }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="form-label">
                    Nombre del evento
                    <input class="field" name="nombre" value="{{ old('nombre') }}" required placeholder="Ej. Torneo de fútbol">
                </label>
                <label class="form-label" style="grid-column:span 2;">
                    Descripción
                    <textarea class="field" name="descripcion" rows="3" placeholder="Describe la actividad brevemente...">{{ old('descripcion') }}</textarea>
                </label>
                <label class="form-label">
                    Fecha del evento
                    <input class="field" name="fecha_evento" type="date" value="{{ old('fecha_evento') }}" required>
                </label>
                <label class="form-label">
                    Lugar <span style="font-weight:400;color:#3d7055;">(opcional)</span>
                    <input class="field" name="lugar" value="{{ old('lugar') }}" placeholder="Ej. Auditorio principal">
                </label>
                <label class="form-label">
                    Cupo máximo <span style="font-weight:400;color:#3d7055;">(opcional)</span>
                    <input class="field" name="cupo_maximo" type="number" min="1" value="{{ old('cupo_maximo') }}" placeholder="Sin límite">
                </label>
            </div>
        </div>

        {{-- Segmentación --}}
        <div class="form-section animate-fade-up-3">
            <p class="form-section-title">Segmentación de audiencia</p>
            <p style="font-size:.8rem;color:#5a9070;margin-bottom:1.25rem;">Todos los criterios se aplican al mostrar e inscribir en el evento.</p>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1.25rem;">
                <label class="form-label">
                    Dirigido a género
                    <select class="field" name="dirigido_a_genero">
                        <option value="TODOS"     @selected(old('dirigido_a_genero', 'TODOS') === 'TODOS')>Todos los géneros</option>
                        <option value="MASCULINO" @selected(old('dirigido_a_genero') === 'MASCULINO')>Solo hombres</option>
                        <option value="FEMENINO"  @selected(old('dirigido_a_genero') === 'FEMENINO')>Solo mujeres</option>
                    </select>
                </label>
                <div></div>
                <label class="form-label">
                    Edad mínima <span style="font-weight:400;color:#3d7055;">(opcional)</span>
                    <input class="field" name="edad_minima" type="number" min="0" max="120" value="{{ old('edad_minima') }}" placeholder="18">
                </label>
                <label class="form-label">
                    Edad máxima <span style="font-weight:400;color:#3d7055;">(opcional)</span>
                    <input class="field" name="edad_maxima" type="number" min="0" max="120" value="{{ old('edad_maxima') }}" placeholder="65">
                </label>
            </div>
            <div style="display:flex;flex-direction:column;gap:.625rem;">
                <label style="display:flex;align-items:center;justify-content:space-between;padding:1rem 1.125rem;background:rgba(0,0,0,.2);border:1px solid var(--border-dark);border-radius:.75rem;cursor:pointer;transition:border-color .15s;" onmouseover="this.style.borderColor='rgba(168,224,99,.25)'" onmouseout="this.style.borderColor='var(--border-dark)'">
                    <div>
                        <span style="font-size:.875rem;font-weight:600;color:#c4ddd0;">Solo padres o madres</span>
                        <span style="display:block;font-size:.75rem;color:#3d7055;margin-top:.125rem;">El evento está dirigido a funcionarios con hijos registrados</span>
                    </div>
                    <input name="requiere_ser_padre_madre" value="1" type="checkbox" @checked(old('requiere_ser_padre_madre')) class="toggle-checkbox">
                </label>
                <label style="display:flex;align-items:center;justify-content:space-between;padding:1rem 1.125rem;background:rgba(0,0,0,.2);border:1px solid var(--border-dark);border-radius:.75rem;cursor:pointer;transition:border-color .15s;" onmouseover="this.style.borderColor='rgba(168,224,99,.25)'" onmouseout="this.style.borderColor='var(--border-dark)'">
                    <div>
                        <span style="font-size:.875rem;font-weight:600;color:#c4ddd0;">Personas con familiares a cargo</span>
                        <span style="display:block;font-size:.75rem;color:#3d7055;margin-top:.125rem;">Para quienes tienen familiares bajo su responsabilidad</span>
                    </div>
                    <input name="requiere_familiar_a_cargo" value="1" type="checkbox" @checked(old('requiere_familiar_a_cargo')) class="toggle-checkbox">
                </label>
            </div>
        </div>

        {{-- Actions --}}
        <div style="display:flex;justify-content:flex-end;gap:.75rem;" class="animate-fade-up-4">
            <a href="{{ route('admin.dashboard') }}" class="btn-ghost">Cancelar</a>
            <button type="submit" class="btn-primary">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                Crear evento
            </button>
        </div>
    </form>
    @endif
</div>
@endsection
