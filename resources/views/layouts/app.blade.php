<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Bienestar SENA')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --sena-950: #071a11;
            --sena-900: #0d2b1d;
            --sena-800: #103c2c;
            --sena-700: #155630;
            --sena-600: #1a6a3b;
            --sena-500: #1d6b3d;
            --sena-400: #23a05a;
            --sena-300: #4ec483;
            --lime: #a8e063;
            --lime-dim: #7db840;
            --surface-dark: #0f2118;
            --surface-card: #142818;
            --border-dark: #1e3d2f;
            --border-light: #d4e8da;
            --text-light: #e8f5ee;
            --text-muted-dark: #5a9070;
            --bg-public: #f2f7f4;
        }
        *, *::before, *::after { box-sizing: border-box; }
        body {
            font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
            -webkit-font-smoothing: antialiased;
        }
        /* Sidebar admin layout */
        .admin-shell {
            display: flex;
            min-height: 100vh;
            background: var(--sena-900);
        }
        .admin-sidebar {
            width: 256px;
            flex-shrink: 0;
            background: var(--sena-950);
            border-right: 1px solid var(--border-dark);
            display: flex;
            flex-direction: column;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
        }
        .admin-content {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
        }
        .admin-topbar {
            background: var(--sena-950);
            border-bottom: 1px solid var(--border-dark);
            padding: 0 2rem;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: .75rem;
            flex-shrink: 0;
        }
        .admin-main {
            flex: 1;
            padding: 2rem;
            overflow-x: hidden;
        }
        /* Public layout */
        .public-shell {
            min-height: 100vh;
            background: var(--bg-public);
        }
        .public-header {
            background: var(--sena-800);
            border-bottom: 1px solid rgba(255,255,255,.06);
        }
        /* Nav links */
        .nav-link {
            display: flex;
            align-items: center;
            gap: .625rem;
            padding: .6rem .875rem;
            border-radius: .625rem;
            font-size: .8125rem;
            font-weight: 500;
            color: var(--text-muted-dark);
            transition: background .15s, color .15s;
            text-decoration: none;
        }
        .nav-link:hover, .nav-link.active {
            background: rgba(168,224,99,.08);
            color: var(--lime);
        }
        .nav-link svg { flex-shrink: 0; }
        /* Animations */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(12px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to   { opacity: 1; }
        }
        .animate-fade-up   { animation: fadeUp .4s ease both; }
        .animate-fade-up-2 { animation: fadeUp .4s .08s ease both; }
        .animate-fade-up-3 { animation: fadeUp .4s .16s ease both; }
        .animate-fade-up-4 { animation: fadeUp .4s .24s ease both; }
        .animate-fade-in   { animation: fadeIn .35s ease both; }
        /* Form inputs */
        .field {
            width: 100%;
            background: rgba(255,255,255,.04);
            border: 1px solid var(--border-dark);
            border-radius: .625rem;
            padding: .7rem 1rem;
            color: var(--text-light);
            font-size: .875rem;
            font-family: inherit;
            outline: none;
            transition: border-color .15s, box-shadow .15s;
        }
        .field::placeholder { color: var(--text-muted-dark); }
        .field:focus {
            border-color: var(--sena-300);
            box-shadow: 0 0 0 3px rgba(78,196,131,.12);
        }
        .field-public {
            width: 100%;
            background: #fff;
            border: 1px solid var(--border-light);
            border-radius: .625rem;
            padding: .75rem 1rem;
            color: #1a3828;
            font-size: .875rem;
            font-family: inherit;
            outline: none;
            transition: border-color .15s, box-shadow .15s;
        }
        .field-public:focus {
            border-color: #23a05a;
            box-shadow: 0 0 0 3px rgba(35,160,90,.12);
        }
        /* Buttons */
        .btn-primary {
            display: inline-flex; align-items: center; gap: .5rem;
            background: var(--lime);
            color: var(--sena-950);
            font-weight: 700; font-size: .8125rem;
            padding: .65rem 1.25rem;
            border-radius: .625rem;
            border: none; cursor: pointer;
            transition: background .15s, transform .1s, box-shadow .15s;
            text-decoration: none;
        }
        .btn-primary:hover {
            background: #bef07a;
            box-shadow: 0 4px 16px rgba(168,224,99,.25);
            transform: translateY(-1px);
        }
        .btn-ghost {
            display: inline-flex; align-items: center; gap: .5rem;
            background: rgba(255,255,255,.06);
            color: var(--text-light);
            font-weight: 500; font-size: .8125rem;
            padding: .65rem 1.125rem;
            border-radius: .625rem;
            border: 1px solid var(--border-dark);
            cursor: pointer;
            transition: background .15s;
            text-decoration: none;
        }
        .btn-ghost:hover { background: rgba(255,255,255,.1); }
        .btn-public {
            display: inline-flex; align-items: center; gap: .5rem;
            background: #1d6b3d;
            color: #fff;
            font-weight: 600; font-size: .875rem;
            padding: .7rem 1.4rem;
            border-radius: .625rem;
            border: none; cursor: pointer;
            transition: background .15s, box-shadow .15s;
            text-decoration: none;
        }
        .btn-public:hover { background: #155630; box-shadow: 0 4px 16px rgba(29,107,61,.3); }
        /* Cards */
        .card {
            background: var(--surface-card);
            border: 1px solid var(--border-dark);
            border-radius: 1rem;
            padding: 1.5rem;
        }
        .card-public {
            background: #fff;
            border: 1px solid var(--border-light);
            border-radius: 1rem;
            box-shadow: 0 2px 16px rgba(16,60,44,.06);
        }
        /* Badges */
        .badge-green {
            display: inline-flex; align-items: center;
            background: rgba(168,224,99,.12);
            color: var(--lime);
            font-size: .7rem; font-weight: 600;
            letter-spacing: .04em; text-transform: uppercase;
            padding: .2rem .55rem;
            border-radius: 99px;
            border: 1px solid rgba(168,224,99,.2);
        }
        /* Eyebrow */
        .eyebrow {
            font-size: .7rem; font-weight: 700;
            letter-spacing: .1em; text-transform: uppercase;
        }
        /* Scrollbar */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--border-dark); border-radius: 99px; }
        /* Alert flashes */
        .alert-success {
            background: rgba(78,196,131,.1);
            border: 1px solid rgba(78,196,131,.2);
            color: #4ec483;
            border-radius: .75rem;
            padding: .75rem 1rem;
            font-size: .875rem;
            margin-bottom: 1.5rem;
        }
        .alert-warning {
            background: rgba(251,191,36,.08);
            border: 1px solid rgba(251,191,36,.2);
            color: #f0bf3a;
            border-radius: .75rem;
            padding: .75rem 1rem;
            font-size: .875rem;
            margin-bottom: 1.5rem;
        }
        .alert-error {
            background: rgba(239,68,68,.08);
            border: 1px solid rgba(239,68,68,.2);
            color: #f87171;
            border-radius: .75rem;
            padding: .75rem 1rem;
            font-size: .875rem;
            margin-bottom: 1.5rem;
        }
    </style>
    @stack('head')
</head>
<body>

@hasSection('sidebar')
{{-- ADMIN LAYOUT con sidebar --}}
<div class="admin-shell">
    {{-- Sidebar --}}
    <aside class="admin-sidebar">
        {{-- Logo --}}
        <div style="padding:1.25rem 1rem 1rem; border-bottom:1px solid var(--border-dark);">
            <a href="{{ auth()->check() ? route('admin.dashboard') : route('acceso') }}" style="display:flex;align-items:center;gap:.75rem;text-decoration:none;">
                <span style="width:38px;height:38px;background:linear-gradient(135deg,var(--lime),#6ecf82);border-radius:.625rem;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:.9rem;color:var(--sena-950);flex-shrink:0;">B</span>
                <span>
                    <span style="display:block;font-size:.8125rem;font-weight:700;color:var(--text-light);letter-spacing:.04em;">BIENESTAR</span>
                    <span style="display:block;font-size:.7rem;color:var(--text-muted-dark);">SENA Reg. Santander</span>
                </span>
            </a>
        </div>
        {{-- Nav --}}
        <nav style="flex:1;padding:.75rem .625rem;display:flex;flex-direction:column;gap:2px;">
            <p class="eyebrow" style="color:var(--text-muted-dark);padding:.5rem .875rem .25rem;margin-bottom:.25rem;">Administración</p>
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
                Panel principal
            </a>
            <a href="{{ route('admin.calendario') }}" class="nav-link {{ request()->routeIs('admin.calendario') ? 'active' : '' }}">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                Calendario
            </a>
            <a href="{{ route('admin.eventos.crear') }}" class="nav-link {{ request()->routeIs('admin.eventos.crear') ? 'active' : '' }}">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 8v8M8 12h8"/></svg>
                Nuevo evento
            </a>
            <a href="{{ route('admin.funcionarios.importar') }}" class="nav-link {{ request()->routeIs('admin.funcionarios.*') ? 'active' : '' }}">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M17 21H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7l5 5v11a2 2 0 0 1-2 2z"/><path d="M14 3v5h5M12 11v6M9 14l3 3 3-3"/></svg>
                Importar datos
            </a>
            <a href="{{ route('admin.periodos.crear') }}" class="nav-link {{ request()->routeIs('admin.periodos.*') ? 'active' : '' }}">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
                Nuevo período
            </a>
            <a href="{{ route('eventos.index') }}" class="nav-link">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                Vista funcionarios
            </a>
        </nav>
        {{-- Logout --}}
        <div style="padding:.75rem .625rem;border-top:1px solid var(--border-dark);">
            <form action="{{ route('salir') }}" method="POST">
                @csrf
                <button type="submit" class="nav-link" style="width:100%;background:none;border:none;cursor:pointer;text-align:left;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    Cerrar sesión
                </button>
            </form>
        </div>
    </aside>

    {{-- Main area --}}
    <div class="admin-content">
        @hasSection('header-actions')
        <div class="admin-topbar">
            @yield('header-actions')
        </div>
        @endif

        <main class="admin-main animate-fade-in">
            @if (session('success'))<div class="alert-success">{{ session('success') }}</div>@endif
            @if (session('mensaje'))<div class="alert-warning">{{ session('mensaje') }}</div>@endif
            @if ($errors->any())<div class="alert-error">{{ $errors->first() }}</div>@endif
            @yield('content')
        </main>

        <footer style="padding:.75rem 2rem;border-top:1px solid var(--border-dark);font-size:.7rem;color:var(--text-muted-dark);">
            Sistema de gestión de bienestar institucional — SENA Regional Santander
        </footer>
    </div>
</div>

@else
{{-- PUBLIC LAYOUT --}}
<div class="public-shell">
    <header class="public-header">
        <div style="max-width:1280px;margin:0 auto;padding:.875rem 1.5rem;display:flex;align-items:center;justify-content:space-between;gap:1rem;">
            <a href="{{ auth()->check() ? route('admin.dashboard') : route('acceso') }}" style="display:flex;align-items:center;gap:.75rem;text-decoration:none;">
                <span style="width:36px;height:36px;background:linear-gradient(135deg,var(--lime),#6ecf82);border-radius:.5rem;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:.875rem;color:var(--sena-950);flex-shrink:0;">B</span>
                <span>
                    <span style="display:block;font-size:.8125rem;font-weight:700;color:#e8f5ee;letter-spacing:.04em;">BIENESTAR</span>
                    <span style="display:block;font-size:.7rem;color:#6b9e82;">SENA Regional Santander</span>
                </span>
            </a>
            @hasSection('header-actions')
            <div style="display:flex;align-items:center;gap:.625rem;">@yield('header-actions')</div>
            @endif
        </div>
    </header>
    <main style="max-width:1280px;margin:0 auto;padding:2rem 1.5rem;">
        @if (session('success'))<div style="background:#eaf7f0;border:1px solid #b9d9c2;color:#1d6b3d;border-radius:.75rem;padding:.75rem 1rem;font-size:.875rem;margin-bottom:1.5rem;">{{ session('success') }}</div>@endif
        @if (session('mensaje'))<div style="background:#fff8e4;border:1px solid #e7d39d;color:#7a5b11;border-radius:.75rem;padding:.75rem 1rem;font-size:.875rem;margin-bottom:1.5rem;">{{ session('mensaje') }}</div>@endif
        @if ($errors->any())<div style="background:#fff0f0;border:1px solid #efc2c2;color:#9a2929;border-radius:.75rem;padding:.75rem 1rem;font-size:.875rem;margin-bottom:1.5rem;">{{ $errors->first() }}</div>@endif
        @yield('content')
    </main>
    <footer style="max-width:1280px;margin:0 auto;padding:.75rem 1.5rem 2rem;font-size:.7rem;color:#8aab98;">Sistema de gestión de bienestar institucional</footer>
</div>
@endif

@stack('scripts')
</body>
</html>
