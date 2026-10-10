@extends('layouts.app')
@section('title', 'Confirmar asistencia | Bienestar SENA')
@section('sidebar', true)
@section('content')
<section class="card" style="max-width:640px;margin:0 auto;">
    <span class="eyebrow" style="color:#5a9070;">Control de asistencia</span>
    <h1 style="margin-top:.375rem;font-size:1.5rem;font-weight:800;color:#e8f5ee;">{{ $inscripcion->evento->nombre }}</h1>
    <p style="margin-top:.75rem;color:#7aaa90;">{{ $inscripcion->funcionario->nombres }} {{ $inscripcion->funcionario->apellidos }} · {{ $inscripcion->funcionario->cedula }}</p>
    <p style="margin-top:.25rem;color:#7aaa90;">{{ $inscripcion->evento->fecha_evento->format('d/m/Y') }} · {{ $inscripcion->funcionario->centro->nombre ?? 'Centro no registrado' }}</p>

    @if($inscripcion->estado !== 'INSCRITO')
    <div class="alert-warning" style="margin-top:1.25rem;">Esta inscripción no está activa.</div>
    @elseif($inscripcion->asistencia?->asistio)
    <div role="status" style="margin-top:1.25rem;padding:1rem;border-radius:.75rem;background:rgba(78,196,131,.1);color:#4ec483;">
        Asistencia registrada el {{ $inscripcion->asistencia->hora_registro?->format('d/m/Y H:i') }}.
    </div>
    @else
    <form method="POST" action="{{ route('admin.asistencia.registrar', $inscripcion->qr_token) }}" style="margin-top:1.5rem;">
        @csrf
        <button class="btn-primary" type="submit">Confirmar asistencia</button>
    </form>
    @endif
</section>
@endsection
