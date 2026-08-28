<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventos de Bienestar — SENA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; background: #f2f7f4; color: #1a3828; -webkit-font-smoothing: antialiased; margin: 0; }

        /* Header */
        .ev-header {
            background: linear-gradient(135deg, #0d2b1d 0%, #103c2c 100%);
            border-bottom: 1px solid rgba(255,255,255,.06);
            position: sticky; top: 0; z-index: 10;
            backdrop-filter: blur(12px);
        }
        .ev-header-inner {
            max-width: 1100px; margin: 0 auto;
            padding: .875rem 1.5rem;
            display: flex; align-items: center; justify-content: space-between; gap: 1rem;
        }

        /* Hero */
        .ev-hero {
            background: linear-gradient(160deg, #0d2b1d 0%, #103c2c 50%, #1a3828 100%);
            padding: 3rem 1.5rem 4rem;
            position: relative; overflow: hidden;
        }
        .ev-hero::after {
            content: '';
            position: absolute; inset: 0;
            background-image: radial-gradient(circle at 70% 50%, rgba(168,224,99,.06) 0%, transparent 60%);
            pointer-events: none;
        }
        .ev-hero-inner { max-width: 1100px; margin: 0 auto; position: relative; z-index: 1; }

        /* Search */
        .ev-search {
            background: rgba(255,255,255,.06);
            border: 1px solid rgba(255,255,255,.12);
            border-radius: .875rem;
            padding: .8rem 1.125rem;
            color: #e8f5ee;
            font-family: 'Inter', sans-serif;
            font-size: .9rem;
            outline: none;
            width: 100%; max-width: 420px;
            transition: border-color .15s, box-shadow .15s;
        }
        .ev-search::placeholder { color: rgba(107,158,130,.7); }
        .ev-search:focus { border-color: #4ec483; box-shadow: 0 0 0 3px rgba(78,196,131,.12); }

        /* Content area */
        .ev-main { max-width: 1100px; margin: 0 auto; padding: 2rem 1.5rem; }

        /* Event card */
        .ev-card {
            background: #fff;
            border: 1px solid #dceae0;
            border-radius: 1rem;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            box-shadow: 0 2px 12px rgba(16,60,44,.05);
            transition: transform .2s, box-shadow .2s, border-color .2s;
            animation: fadeUp .4s ease both;
        }
        .ev-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(16,60,44,.12);
            border-color: #a8e063;
        }
        @keyframes fadeUp { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }

        .ev-card-date {
            background: linear-gradient(135deg, #103c2c, #0d2b1d);
            padding: 1.125rem 1.25rem;
            display: flex; align-items: center; gap: .875rem;
        }
        .ev-date-badge {
            text-align: center;
            background: rgba(168,224,99,.15);
            border: 1px solid rgba(168,224,99,.25);
            border-radius: .5rem;
            padding: .375rem .75rem;
            flex-shrink: 0;
        }
        .ev-card-body { padding: 1.25rem 1.25rem 1rem; flex: 1; }
        .ev-card-footer { padding: .875rem 1.25rem; border-top: 1px solid #edf2ee; display: flex; align-items: center; justify-content: space-between; gap: .75rem; }

        /* Inscrito badge */
        .badge-inscrito {
            display: inline-flex; align-items: center; gap: .375rem;
            background: #eaf7f0; border: 1px solid #b9d9c2;
            color: #1d6b3d; font-size: .78rem; font-weight: 700;
            padding: .45rem .875rem; border-radius: .5rem;
        }
        .btn-inscribir {
            display: inline-flex; align-items: center; gap: .5rem;
            background: linear-gradient(135deg, #a8e063, #7db840);
            color: #071a11; font-weight: 700; font-size: .8125rem;
            padding: .55rem 1.125rem; border-radius: .5rem;
            border: none; cursor: pointer; font-family: 'Inter', sans-serif;
            transition: opacity .15s, transform .1s;
        }
        .btn-inscribir:hover { opacity: .9; transform: translateY(-1px); }
        .btn-encuesta {
            display: inline-flex; align-items: center; gap: .375rem;
            background: #1d6b3d; color: #fff;
            font-size: .78rem; font-weight: 600;
            padding: .45rem .875rem; border-radius: .5rem;
            text-decoration: none; transition: background .15s;
        }
        .btn-encuesta:hover { background: #155630; }

        /* Nav link */
        .nav-item {
            display: inline-flex; align-items: center; gap: .5rem;
            padding: .5rem .875rem; border-radius: .5rem;
            font-size: .8125rem; font-weight: 500; color: rgba(232,245,238,.7);
            text-decoration: none; border: 1px solid rgba(255,255,255,.1);
            transition: background .15s, color .15s;
        }
        .nav-item:hover { background: rgba(255,255,255,.08); color: #e8f5ee; }
    </style>
</head>
<body>

{{-- Header --}}
<header class="ev-header">
    <div class="ev-header-inner">
        <div style="display:flex;align-items:center;gap:.75rem;">
            <span style="width:34px;height:34px;background:linear-gradient(135deg,#a8e063,#6ecf82);border-radius:.5rem;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:.875rem;color:#071a11;">B</span>
            <div>
                <span style="display:block;font-size:.8rem;font-weight:700;color:#e8f5ee;letter-spacing:.05em;">BIENESTAR</span>
                <span style="display:block;font-size:.68rem;color:#5a9070;">SENA Regional Santander</span>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:.5rem;flex-wrap:wrap;">
            <a href="{{ route('funcionario.formulario') }}" class="nav-item">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                Mis datos
            </a>
            <form action="{{ route('salir') }}" method="POST">
                @csrf
                <button type="submit" class="nav-item" style="background:none;border:1px solid rgba(255,255,255,.1);cursor:pointer;font-family:'Inter',sans-serif;">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    Salir
                </button>
            </form>
        </div>
    </div>
</header>

{{-- Hero --}}
<div class="ev-hero">
    <div class="ev-hero-inner">
        <span style="font-size:.7rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:#a8e063;">Portal de bienestar</span>
        <h1 style="margin-top:.5rem;font-size:clamp(1.75rem,4vw,2.5rem);font-weight:800;color:#e8f5ee;line-height:1.15;letter-spacing:-.02em;">
            Hola, {{ $funcionario->nombres }}
        </h1>
        <p style="margin-top:.625rem;font-size:.9375rem;color:#6b9e82;max-width:420px;line-height:1.6;">
            Descubre las actividades disponibles e inscríbete en las que más te interesen.
        </p>

        @if(session('success'))
        <div style="margin-top:1.25rem;background:rgba(78,196,131,.12);border:1px solid rgba(78,196,131,.2);color:#4ec483;border-radius:.75rem;padding:.75rem 1rem;font-size:.875rem;max-width:480px;">
            {{ session('success') }}
        </div>
        @endif
        @if(session('mensaje'))
        <div style="margin-top:1.25rem;background:rgba(251,191,36,.08);border:1px solid rgba(251,191,36,.18);color:#f0bf3a;border-radius:.75rem;padding:.75rem 1rem;font-size:.875rem;max-width:480px;">
            {{ session('mensaje') }}
        </div>
        @endif

        {{-- Search --}}
        <div style="margin-top:1.75rem;position:relative;display:inline-block;width:100%;max-width:440px;">
            <svg style="position:absolute;left:.875rem;top:50%;transform:translateY(-50%);pointer-events:none;" width="16" height="16" fill="none" stroke="#5a9070" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            <input
                id="buscar-evento"
                type="search"
                value="{{ request('buscar') }}"
                placeholder="Buscar evento por nombre o lugar..."
                class="ev-search"
                style="padding-left:2.5rem;"
            >
            <span id="busqueda-estado" style="position:absolute;right:.875rem;top:50%;transform:translateY(-50%);font-size:.75rem;color:#5a9070;pointer-events:none;"></span>
        </div>
    </div>
</div>

{{-- Events grid --}}
<main class="ev-main">
    @if($eventos->isEmpty())
    <div style="background:#fff;border:1px dashed #c5dbc9;border-radius:1rem;padding:3rem;text-align:center;">
        <div style="width:56px;height:56px;background:#edf5e4;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
            <svg width="24" height="24" fill="none" stroke="#a8e063" stroke-width="1.8" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
        </div>
        <p style="font-size:.9375rem;font-weight:600;color:#1a3828;">No hay eventos disponibles en este momento.</p>
        <p style="font-size:.875rem;color:#8aab98;margin-top:.375rem;">Vuelve pronto — las actividades se actualizan regularmente.</p>
    </div>
    @else
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:1.125rem;">
        @forelse($eventos as $i => $evento)
        <article class="ev-card" style="animation-delay: {{ $i * 0.06 }}s;">
            <div class="ev-card-date">
                <div class="ev-date-badge">
                    <span style="display:block;font-size:1.375rem;font-weight:800;color:#a8e063;line-height:1;">{{ $evento->fecha_evento->format('d') }}</span>
                    <span style="display:block;font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#5a9070;">{{ $evento->fecha_evento->format('M') }}</span>
                </div>
                <div>
                    <span style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#a8e063;">{{ $evento->fecha_evento->format('Y') }}</span>
                    @if($evento->lugar)
                    <p style="font-size:.78rem;color:#5a9070;margin-top:.125rem;">
                        <svg style="display:inline;vertical-align:middle;margin-right:.25rem;" width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        {{ $evento->lugar }}
                    </p>
                    @endif
                </div>
            </div>

            <div class="ev-card-body">
                <h2 style="font-size:1rem;font-weight:700;color:#1a3828;line-height:1.3;">{{ $evento->nombre }}</h2>
                @if($evento->descripcion)
                <p style="font-size:.8125rem;color:#5a7a65;margin-top:.5rem;line-height:1.6;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">{{ $evento->descripcion }}</p>
                @endif
            </div>

            <div class="ev-card-footer">
                @if(in_array($evento->id, $misInscripcionesIds, true))
                <div style="display:flex;align-items:center;gap:.5rem;flex-wrap:wrap;">
                    <span class="badge-inscrito">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                        Ya inscrito
                    </span>
                    @foreach($evento->encuestas as $encuesta)
                    <a href="{{ route('encuestas.mostrar', $encuesta) }}" class="btn-encuesta">
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                        Encuesta
                    </a>
                    @endforeach
                </div>
                @else
                <form action="{{ route('eventos.inscribir', $evento) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-inscribir">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                        Inscribirme
                    </button>
                </form>
                @endif
            </div>
        </article>
        @empty
        <p style="grid-column:1/-1;text-align:center;color:#8aab98;padding:2rem;">No hay resultados para tu búsqueda.</p>
        @endforelse
    </div>
    @endif
</main>

<footer style="max-width:1100px;margin:0 auto;padding:.75rem 1.5rem 2rem;font-size:.75rem;color:#8aab98;">
    Sistema de gestión de bienestar institucional — SENA Regional Santander
</footer>

<script>
const buscadorEvento = document.getElementById('buscar-evento');
let temporizadorBusqueda;
buscadorEvento?.addEventListener('input', () => {
    clearTimeout(temporizadorBusqueda);
    document.getElementById('busqueda-estado').textContent = '⟳';
    temporizadorBusqueda = setTimeout(() => {
        const params = new URLSearchParams(window.location.search);
        const t = buscadorEvento.value.trim();
        t ? params.set('buscar', t) : params.delete('buscar');
        window.location.search = params.toString();
    }, 350);
});
</script>
</body>
</html>
