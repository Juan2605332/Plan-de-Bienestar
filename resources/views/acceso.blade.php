@extends('layouts.app')

@section('title', 'Acceso | Bienestar SENA')

@section('content')
@endsection

@push('head')
<style>
    body { background: #071a11 !important; }
    .public-shell, header.public-header { display: none !important; }
    .login-wrap {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1.5rem;
        background: #071a11;
        position: relative;
        overflow: hidden;
    }
    /* Animated background blobs */
    .login-blob {
        position: absolute;
        border-radius: 50%;
        filter: blur(80px);
        opacity: .18;
        pointer-events: none;
    }
    .login-blob-1 {
        width: 500px; height: 500px;
        background: radial-gradient(circle, #23a05a, transparent 70%);
        top: -120px; left: -120px;
        animation: blobFloat1 12s ease-in-out infinite alternate;
    }
    .login-blob-2 {
        width: 400px; height: 400px;
        background: radial-gradient(circle, #a8e063, transparent 70%);
        bottom: -80px; right: -80px;
        animation: blobFloat2 10s ease-in-out infinite alternate;
    }
    .login-blob-3 {
        width: 250px; height: 250px;
        background: radial-gradient(circle, #103c2c, transparent 70%);
        top: 50%; left: 60%;
        animation: blobFloat3 14s ease-in-out infinite alternate;
    }
    @keyframes blobFloat1 { from { transform: translate(0,0) scale(1); } to { transform: translate(40px,30px) scale(1.08); } }
    @keyframes blobFloat2 { from { transform: translate(0,0) scale(1); } to { transform: translate(-30px,-20px) scale(1.05); } }
    @keyframes blobFloat3 { from { transform: translate(0,0); } to { transform: translate(-20px,25px); } }

    /* Grid pattern overlay */
    .login-grid {
        position: absolute; inset: 0;
        background-image:
            linear-gradient(rgba(168,224,99,.035) 1px, transparent 1px),
            linear-gradient(90deg, rgba(168,224,99,.035) 1px, transparent 1px);
        background-size: 40px 40px;
        pointer-events: none;
    }

    .login-card {
        position: relative; z-index: 2;
        width: 100%; max-width: 960px;
        background: rgba(13,43,29,.7);
        backdrop-filter: blur(24px) saturate(1.5);
        border: 1px solid rgba(168,224,99,.1);
        border-radius: 1.5rem;
        box-shadow: 0 32px 80px rgba(0,0,0,.5), 0 0 0 1px rgba(255,255,255,.03);
        display: grid;
        overflow: hidden;
        animation: fadeUp .5s ease both;
    }
    @media (min-width: 768px) {
        .login-card { grid-template-columns: 1fr 1fr; }
    }
    @keyframes fadeUp {
        from { opacity:0; transform: translateY(20px); }
        to   { opacity:1; transform: translateY(0); }
    }

    /* Left panel */
    .login-left {
        background: linear-gradient(145deg, #0d2b1d 0%, #071a11 100%);
        padding: 3rem 2.5rem;
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .login-left-deco {
        position: absolute;
        width: 240px; height: 240px;
        border-radius: 50%;
        border: 40px solid rgba(168,224,99,.07);
        top: -60px; right: -60px;
        pointer-events: none;
    }
    .login-left-deco2 {
        position: absolute;
        width: 150px; height: 150px;
        border-radius: 50%;
        border: 25px solid rgba(35,160,90,.06);
        bottom: 40px; left: -40px;
        pointer-events: none;
    }
    .login-stats {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: .875rem;
        margin-top: 2.5rem;
    }
    .login-stat {
        border-left: 2px solid rgba(168,224,99,.4);
        padding-left: .75rem;
    }

    /* Right panel */
    .login-right {
        padding: 3rem 2.5rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .login-input {
        width: 100%;
        background: rgba(255,255,255,.04);
        border: 1px solid rgba(255,255,255,.1);
        border-radius: .625rem;
        padding: .8rem 1rem;
        color: #e8f5ee;
        font-size: .875rem;
        font-family: 'Inter', sans-serif;
        outline: none;
        transition: border-color .15s, box-shadow .15s, background .15s;
        margin-top: .5rem;
    }
    .login-input::placeholder { color: rgba(107,158,130,.7); }
    .login-input:focus {
        border-color: #4ec483;
        background: rgba(78,196,131,.05);
        box-shadow: 0 0 0 3px rgba(78,196,131,.12);
    }
    .login-label {
        display: block;
        font-size: .8rem;
        font-weight: 600;
        color: #5a9070;
        letter-spacing: .03em;
    }
</style>

<div class="login-wrap">
    <div class="login-blob login-blob-1"></div>
    <div class="login-blob login-blob-2"></div>
    <div class="login-blob login-blob-3"></div>
    <div class="login-grid"></div>

    <div class="login-card">
        {{-- Panel izquierdo --}}
        <div class="login-left">
            <div class="login-left-deco"></div>
            <div class="login-left-deco2"></div>

            <div style="position:relative;">
                {{-- Logo --}}
                <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:2.5rem;">
                    <span style="width:42px;height:42px;background:linear-gradient(135deg,#a8e063,#6ecf82);border-radius:.75rem;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:1rem;color:#071a11;flex-shrink:0;">B</span>
                    <span>
                        <span style="display:block;font-size:.875rem;font-weight:700;color:#e8f5ee;letter-spacing:.05em;">BIENESTAR</span>
                        <span style="display:block;font-size:.7rem;color:#5a9070;">SENA Regional Santander</span>
                    </span>
                </div>

                <span style="font-size:.7rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:#a8e063;">Portal institucional</span>
                <h1 style="margin-top:.875rem;font-size:clamp(1.6rem,3vw,2.25rem);font-weight:700;line-height:1.2;color:#e8f5ee;">
                    Bienestar<br>que transforma<br><span style="color:#a8e063;">comunidad.</span>
                </h1>
                <p style="margin-top:1.25rem;font-size:.875rem;line-height:1.7;color:#5a9070;max-width:280px;">
                    Gestiona actividades, descubre oportunidades y participa en las iniciativas de tu entidad.
                </p>
            </div>

            <div class="login-stats" style="position:relative;">
                <div class="login-stat">
                    <strong style="display:block;font-size:1.5rem;font-weight:700;color:#e8f5ee;line-height:1;">24/7</strong>
                    <span style="font-size:.75rem;color:#5a9070;">Acceso digital</span>
                </div>
                <div class="login-stat">
                    <strong style="display:block;font-size:1.5rem;font-weight:700;color:#e8f5ee;line-height:1;">SENA</strong>
                    <span style="font-size:.75rem;color:#5a9070;">Colombia</span>
                </div>
            </div>
        </div>

        {{-- Panel derecho --}}
        <div class="login-right">
            <div style="margin-bottom:2rem;">
                <span style="font-size:.7rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:#5a9070;">Ingreso seguro</span>
                <h2 style="margin-top:.5rem;font-size:1.6rem;font-weight:700;color:#e8f5ee;">Bienvenido</h2>
                <p style="margin-top:.375rem;font-size:.875rem;color:#5a9070;">Usa tus credenciales institucionales para continuar.</p>
            </div>

            <form action="{{ route('ingresar') }}" method="POST" style="display:flex;flex-direction:column;gap:1.25rem;">
                @csrf
                <div>
                    <label class="login-label">Correo institucional</label>
                    <input
                        type="email"
                        name="usuario"
                        value="{{ old('usuario') }}"
                        placeholder="admin@sena.edu.co"
                        required
                        autofocus
                        class="login-input"
                    >
                </div>
                <div>
                    <label class="login-label">Contraseña</label>
                    <input
                        type="password"
                        name="password"
                        placeholder="••••••••"
                        required
                        class="login-input"
                    >
                </div>

                @if ($errors->any())
                <div style="background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.2);color:#f87171;border-radius:.625rem;padding:.625rem .875rem;font-size:.8rem;">
                    {{ $errors->first() }}
                </div>
                @endif

                <div style="display:flex;align-items:center;justify-content:space-between;gap:.75rem;padding-top:.5rem;">
                    <button type="reset" style="background:transparent;border:none;font-size:.8rem;color:#5a9070;cursor:pointer;font-family:inherit;padding:.5rem;border-radius:.5rem;transition:color .15s;" onmouseover="this.style.color='#e8f5ee'" onmouseout="this.style.color='#5a9070'">Limpiar</button>
                    <button
                        type="submit"
                        style="background:linear-gradient(135deg,#a8e063,#7db840);color:#071a11;font-weight:700;font-size:.875rem;padding:.75rem 1.75rem;border-radius:.625rem;border:none;cursor:pointer;font-family:inherit;transition:opacity .15s, transform .1s, box-shadow .15s;box-shadow:0 4px 20px rgba(168,224,99,.25);"
                        onmouseover="this.style.opacity='.9';this.style.transform='translateY(-1px)'"
                        onmouseout="this.style.opacity='1';this.style.transform='translateY(0)'"
                    >
                        Ingresar al sistema →
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endpush
