@extends('layouts.app')
@section('title', 'Importar funcionarios | Bienestar SENA')
@section('sidebar', true)
@section('content')
<div style="max-width:640px;" class="animate-fade-up">
    <div style="margin-bottom:1.5rem;">
        <span class="eyebrow" style="color:#5a9070;">Directorio</span>
        <h1 style="margin-top:.375rem;font-size:1.6rem;font-weight:800;color:#e8f5ee;letter-spacing:-.02em;">Importar funcionarios</h1>
        <p style="margin-top:.25rem;font-size:.875rem;color:#5a9070;">Carga un archivo XLSX o CSV para actualizar el directorio institucional.</p>
    </div>

    <form class="animate-fade-up-2" method="POST" enctype="multipart/form-data" action="{{ route('admin.funcionarios.importar.guardar') }}" style="display:flex;flex-direction:column;gap:1rem;">
        @csrf

        {{-- Drop zone --}}
        <div style="background:var(--surface-card);border:1px solid var(--border-dark);border-radius:1rem;padding:1.5rem;">
            <label id="drop-zone" for="file-input" style="display:flex;flex-direction:column;align-items:center;justify-content:center;gap:1rem;border:2px dashed rgba(168,224,99,.2);border-radius:.875rem;padding:3rem 2rem;text-align:center;cursor:pointer;transition:border-color .15s,background .15s;" onmouseover="this.style.borderColor='rgba(168,224,99,.45)';this.style.background='rgba(168,224,99,.04)'" onmouseout="this.style.borderColor='rgba(168,224,99,.2)';this.style.background='transparent'">
                <div style="width:64px;height:64px;background:rgba(168,224,99,.1);border:1px solid rgba(168,224,99,.2);border-radius:1rem;display:flex;align-items:center;justify-content:center;">
                    <svg width="28" height="28" fill="none" stroke="#a8e063" stroke-width="1.6" viewBox="0 0 24 24">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="17 8 12 3 7 8"/>
                        <line x1="12" y1="3" x2="12" y2="15"/>
                    </svg>
                </div>
                <div>
                    <p style="font-size:.9375rem;font-weight:700;color:#e8f5ee;">Selecciona o arrastra tu archivo</p>
                    <p style="font-size:.8rem;color:#5a9070;margin-top:.375rem;">Formatos: XLSX o CSV · Máximo 5 MB</p>
                </div>
                <input id="file-input" type="file" name="archivo" accept=".xlsx,.csv" required style="display:none;">
                <div id="file-name" style="display:none;background:rgba(168,224,99,.1);border:1px solid rgba(168,224,99,.2);border-radius:.5rem;padding:.5rem 1rem;font-size:.8125rem;font-weight:600;color:#a8e063;"></div>
            </label>
        </div>

        {{-- Expected columns info --}}
        <div style="background:rgba(0,0,0,.2);border:1px solid var(--border-dark);border-radius:.875rem;padding:1.125rem 1.25rem;">
            <p style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#5a9070;margin-bottom:.75rem;">Encabezados esperados</p>
            <div style="display:flex;flex-wrap:wrap;gap:.375rem;">
                @foreach(['cedula','nombres','apellidos','genero','fecha_nacimiento','email','telefono','direccion_residencia','eps','fondo_pension','tallas','tipo_cargo','tipo_vinculacion','centro_formacion','municipio'] as $col)
                <span style="background:rgba(168,224,99,.08);border:1px solid rgba(168,224,99,.14);color:#a8e063;font-size:.7rem;font-weight:600;font-family:monospace;padding:.2rem .55rem;border-radius:.375rem;">{{ $col }}</span>
                @endforeach
            </div>
        </div>

        <div style="display:flex;justify-content:flex-end;gap:.75rem;" class="animate-fade-up-3">
            <a href="{{ route('admin.dashboard') }}" class="btn-ghost">Cancelar</a>
            <button type="submit" class="btn-primary">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                Importar archivo
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.getElementById('file-input').addEventListener('change', function() {
    const nameEl = document.getElementById('file-name');
    if (this.files.length) {
        nameEl.textContent = '📄 ' + this.files[0].name;
        nameEl.style.display = 'block';
    } else {
        nameEl.style.display = 'none';
    }
});
</script>
@endpush
@endsection
