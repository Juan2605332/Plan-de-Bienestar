@extends('layouts.app')
@section('title', 'Calendario anual | Bienestar SENA')
@section('content')
<div style="max-width:1000px;margin:0 auto;">
    <div style="display:flex;align-items:flex-end;justify-content:space-between;gap:1rem;flex-wrap:wrap;margin-bottom:1.5rem;">
        <div>
            <span style="font-size:.7rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:#5a9070;">Plan de bienestar</span>
            <h1 style="margin-top:.375rem;font-size:1.75rem;font-weight:800;color:#1a3828;">Calendario de actividades {{ now('America/Bogota')->year }}</h1>
            <p style="margin-top:.375rem;color:#5a7a65;">Consulta las jornadas y actividades programadas durante el año.</p>
        </div>
        <a href="{{ route('eventos.index') }}" class="btn-public">Ver inscripciones</a>
    </div>

    @forelse($eventos as $mes => $actividades)
    <section style="margin-bottom:1.25rem;">
        <h2 style="margin-bottom:.625rem;font-size:1rem;font-weight:700;color:#1d6b3d;text-transform:capitalize;">{{ $mes }}</h2>
        <div style="display:grid;gap:.625rem;">
            @foreach($actividades as $evento)
            <article style="display:flex;align-items:center;gap:1rem;background:#fff;border:1px solid #dceae0;border-radius:.75rem;padding:1rem;">
                <div style="min-width:56px;text-align:center;background:#edf5e4;border-radius:.625rem;padding:.5rem;color:#1d6b3d;">
                    <strong style="display:block;font-size:1.2rem;">{{ $evento->fecha_evento->format('d') }}</strong>
                    <span style="font-size:.7rem;">{{ $evento->fecha_evento->format('Y') }}</span>
                </div>
                <div>
                    <h3 style="font-weight:700;color:#1a3828;">{{ $evento->nombre }}</h3>
                    @if($evento->lugar)<p style="font-size:.8rem;color:#5a7a65;">{{ $evento->lugar }}</p>@endif
                    @if($evento->descripcion)<p style="margin-top:.25rem;font-size:.82rem;color:#5a7a65;">{{ $evento->descripcion }}</p>@endif
                </div>
            </article>
            @endforeach
        </div>
    </section>
    @empty
    <p style="padding:2rem;text-align:center;background:#fff;border:1px dashed #c5dbc9;border-radius:1rem;color:#5a7a65;">Aún no hay actividades programadas para este año.</p>
    @endforelse
</div>
@endsection
