@extends('layouts.app')
@section('title', 'Inscritos | Bienestar SENA')
@section('sidebar', true)
@section('content')
<div class="animate-fade-up">
    <div style="display:flex;align-items:flex-end;justify-content:space-between;gap:1rem;flex-wrap:wrap;margin-bottom:1.5rem;">
        <div>
            <span class="eyebrow" style="color:#5a9070;">Seguimiento</span>
            <h1 style="margin-top:.375rem;font-size:1.6rem;font-weight:800;color:#e8f5ee;letter-spacing:-.02em;">Personas inscritas</h1>
            <p style="margin-top:.25rem;font-size:.875rem;color:#5a9070;">{{ $evento->nombre }}</p>
        </div>
        <a href="{{ route('admin.inscritos.exportar', $evento) }}" class="btn-primary">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Exportar XLSX
        </a>
    </div>

    <div class="card animate-fade-up-2" style="padding:0;overflow:hidden;">
        <div style="padding:.875rem 1.5rem;border-bottom:1px solid var(--border-dark);display:flex;align-items:center;gap:.75rem;">
            <span class="badge-green">{{ $inscripciones->count() }} registros</span>
            <span style="font-size:.78rem;color:#3d7055;">· ordenados por inscripción más reciente</span>
        </div>
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;font-size:.8125rem;">
                <thead>
                    <tr style="border-bottom:1px solid var(--border-dark);">
                        <th style="padding:.75rem 1.5rem;font-size:.7rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:#5a9070;text-align:left;">Funcionario</th>
                        <th style="padding:.75rem 1rem;font-size:.7rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:#5a9070;text-align:left;">Cédula</th>
                        <th style="padding:.75rem 1rem;font-size:.7rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:#5a9070;text-align:left;">Fecha inscripción</th>
                        <th style="padding:.75rem 1.5rem;font-size:.7rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:#5a9070;text-align:left;">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inscripciones as $inscripcion)
                    <tr style="border-bottom:1px solid rgba(30,64,48,.4);transition:background .12s;" onmouseover="this.style.background='rgba(168,224,99,.03)'" onmouseout="this.style.background='transparent'">
                        <td style="padding:.875rem 1.5rem;">
                            <div style="display:flex;align-items:center;gap:.75rem;">
                                <span style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#1d6b3d,#23a05a);display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:700;color:#e8f5ee;flex-shrink:0;">
                                    {{ strtoupper(substr($inscripcion->funcionario->nombres, 0, 1)) }}
                                </span>
                                <div>
                                    <span style="font-weight:600;color:#e8f5ee;">{{ $inscripcion->funcionario->nombres }} {{ $inscripcion->funcionario->apellidos }}</span>
                                </div>
                            </div>
                        </td>
                        <td style="padding:.875rem 1rem;color:#7aaa90;font-family:monospace;font-size:.8rem;">{{ $inscripcion->funcionario->cedula }}</td>
                        <td style="padding:.875rem 1rem;color:#7aaa90;">{{ $inscripcion->fecha_inscripcion->format('d/m/Y H:i') }}</td>
                        <td style="padding:.875rem 1.5rem;">
                            <span class="badge-green">{{ $inscripcion->estado }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align:center;color:#3d7055;padding:3rem;font-size:.875rem;">No hay inscritos para este evento.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
