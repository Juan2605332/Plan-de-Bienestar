<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Actualización de datos — Bienestar SENA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; background: #f2f7f4; color: #1a3828; -webkit-font-smoothing: antialiased; margin: 0; }

        /* Header */
        .fn-header {
            background: linear-gradient(135deg, #0d2b1d, var(--profile-accent, #103c2c));
            border-bottom: 1px solid rgba(255,255,255,.06);
            position: sticky; top: 0; z-index: 20;
        }
        .fn-header-inner {
            max-width: 900px; margin: 0 auto;
            padding: .875rem 1.5rem;
            display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;
        }
        .fn-badge {
            display: inline-flex; align-items: center; gap: .375rem;
            background: color-mix(in srgb, var(--profile-accent, #1d6b3d) 12%, transparent); border: 1px solid color-mix(in srgb, var(--profile-accent, #1d6b3d) 30%, transparent);
            color: var(--profile-accent, #1d6b3d); font-size: .7rem; font-weight: 700;
            letter-spacing: .06em; text-transform: uppercase;
            padding: .25rem .625rem; border-radius: 99px;
        }
        .nav-btn {
            display: inline-flex; align-items: center; gap: .5rem;
            padding: .45rem .875rem; border-radius: .5rem;
            font-size: .8rem; font-weight: 500; color: rgba(232,245,238,.7);
            border: 1px solid rgba(255,255,255,.1);
            background: transparent; cursor: pointer;
            font-family: 'Inter', sans-serif;
            text-decoration: none;
            transition: background .15s, color .15s;
        }
        .nav-btn:hover { background: rgba(255,255,255,.08); color: #e8f5ee; }

        /* Main */
        .fn-main { max-width: 900px; margin: 0 auto; padding: 2rem 1.5rem; }

        /* Section tabs */
        .fn-section {
            background: #fff;
            border: 1px solid #dceae0;
            border-radius: 1rem;
            overflow: hidden;
            margin-bottom: 1rem;
        }
        .fn-section-header {
            display: flex; align-items: center; gap: .75rem;
            padding: 1rem 1.375rem;
            background: linear-gradient(135deg, #f2f7f4, #eaf5ec);
            border-bottom: 1px solid #dceae0;
        }
        .fn-section-icon {
            width: 36px; height: 36px;
            border-radius: .625rem;
            background: linear-gradient(135deg, #103c2c, #1d6b3d);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .fn-section-body { padding: 1.375rem; }

        /* Fields */
        .fn-field {
            width: 100%;
            background: #f8fbf8;
            border: 1px solid #d4e8da;
            border-radius: .625rem;
            padding: .75rem 1rem;
            color: #1a3828;
            font-family: 'Inter', sans-serif;
            font-size: .875rem;
            outline: none;
            transition: border-color .15s, box-shadow .15s;
        }
        .fn-field:focus { border-color: #23a05a; box-shadow: 0 0 0 3px rgba(35,160,90,.1); }
        .fn-label { display: flex; flex-direction: column; gap: .375rem; font-size: .8rem; font-weight: 600; color: #3d7055; }

        /* Talla chip */
        .talla-chip {
            display: inline-flex; align-items: center; justify-content: center;
            width: 48px; height: 48px;
            border: 2px solid #d4e8da;
            border-radius: .5rem;
            font-size: .875rem; font-weight: 700; color: #426b53;
            cursor: pointer;
            transition: border-color .15s, background .15s, color .15s;
        }
        .talla-chip:hover { border-color: #23a05a; }
        .talla-chip.selected { border-color: #1d6b3d; background: #1d6b3d; color: #a8e063; }

        /* Familiar row */
        .familiar-row {
            display: flex; align-items: center; justify-content: space-between; gap: .75rem;
            padding: .875rem 1rem;
            background: #f8fbf8;
            border: 1px solid #d4e8da;
            border-radius: .75rem;
            font-size: .875rem;
        }
        .familiar-avatar {
            width: 36px; height: 36px; border-radius: 50%;
            background: linear-gradient(135deg, #103c2c, #1d6b3d);
            display: flex; align-items: center; justify-content: center;
            font-size: .75rem; font-weight: 700; color: #a8e063;
            flex-shrink: 0;
        }

        /* Modal */
        .fn-modal-overlay {
            position: fixed; inset: 0; z-index: 50;
            background: rgba(7,26,17,.7);
            backdrop-filter: blur(4px);
            display: flex; align-items: center; justify-content: center;
            padding: 1rem;
        }
        .fn-modal {
            background: #fff;
            border: 1px solid #dceae0;
            border-radius: 1.25rem;
            padding: 1.75rem;
            width: 100%; max-width: 560px;
            box-shadow: 0 24px 64px rgba(0,0,0,.2);
            animation: fadeUp .25s ease;
        }
        @keyframes fadeUp { from { opacity:0; transform:translateY(12px); } to { opacity:1; transform:translateY(0); } }

        /* Buttons */
        .btn-submit {
            display: inline-flex; align-items: center; gap: .5rem;
            background: var(--profile-accent, #1d6b3d);
            color: #fff; font-weight: 700; font-size: .875rem;
            padding: .75rem 1.5rem; border-radius: .625rem;
            border: none; cursor: pointer; font-family: 'Inter', sans-serif;
            transition: opacity .15s, box-shadow .15s;
        }
        .btn-submit:hover { opacity: .9; box-shadow: 0 4px 16px rgba(29,107,61,.3); }
        .btn-secondary {
            display: inline-flex; align-items: center; gap: .5rem;
            background: #f2f7f4; color: #426b53; font-weight: 600; font-size: .875rem;
            padding: .75rem 1.25rem; border-radius: .625rem;
            border: 1px solid #d4e8da; cursor: pointer; font-family: 'Inter', sans-serif;
            text-decoration: none; transition: background .15s;
        }
        .btn-secondary:hover { background: #e4eee5; }
        .btn-add {
            display: inline-flex; align-items: center; gap: .5rem;
            background: var(--profile-accent, #103c2c); color: #fff; font-weight: 600; font-size: .8125rem;
            padding: .55rem 1rem; border-radius: .625rem;
            border: none; cursor: pointer; font-family: 'Inter', sans-serif;
            transition: background .15s;
        }
        .btn-add:hover { background: #0d2b1d; }
    </style>
</head>
<body style="--profile-accent: {{ $usuario->profile_color }}">

{{-- Header --}}
<header class="fn-header">
    <div class="fn-header-inner">
        <div style="display:flex;align-items:center;gap:.75rem;">
            <span style="width:34px;height:34px;background:linear-gradient(135deg,#a8e063,#6ecf82);border-radius:.5rem;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:.875rem;color:#071a11;">B</span>
            <div>
                <span style="display:block;font-size:.8rem;font-weight:700;color:#e8f5ee;letter-spacing:.05em;">BIENESTAR</span>
                <span style="display:block;font-size:.68rem;color:#5a9070;">SENA Regional Santander</span>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:.5rem;flex-wrap:wrap;">
            <span class="fn-badge">
                <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                CC {{ $funcionario->cedula }}
            </span>
            <a href="{{ route('eventos.calendario') }}" class="nav-btn">Calendario</a>
            <a href="{{ route('eventos.index') }}" class="nav-btn">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                Eventos
            </a>
            <form action="{{ route('salir') }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="nav-btn">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    Salir
                </button>
            </form>
        </div>
    </div>
</header>

<main class="fn-main">
    @if (session('success'))
        <div role="status" style="background:#eaf7f0;border:1px solid #b9d9c2;color:#1d6b3d;border-radius:.75rem;padding:.75rem 1rem;margin-bottom:1rem;">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div role="alert" style="background:#fff0f0;border:1px solid #efc2c2;color:#9a2929;border-radius:.75rem;padding:.75rem 1rem;margin-bottom:1rem;">{{ $errors->first() }}</div>
    @endif
    {{-- Page title --}}
    <div style="margin-bottom:1.75rem;">
        <span style="font-size:.7rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:#8aab98;">Perfil institucional</span>
        <h1 style="margin-top:.375rem;font-size:1.6rem;font-weight:800;color:#1a3828;letter-spacing:-.02em;">Actualizar mis datos</h1>
        <p style="margin-top:.25rem;font-size:.875rem;color:#5a7a65;">{{ $funcionario->nombres }} {{ $funcionario->apellidos }}</p>
    </div>
    @unless ($puedeActualizar)
        <div role="status" style="background:#fff8e8;border:1px solid #ead8a8;color:#765a12;border-radius:.75rem;padding:.75rem 1rem;margin-bottom:1rem;">
            modo de solo lectura para los datos institucionales: el período de actualización está cerrado. Puedes actualizar tu perfil personal y contraseña.
        </div>
    @endunless

    <form action="{{ route('funcionario.guardar') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="fn-section">
            <div class="fn-section-header">
                <div class="fn-section-icon"><svg width="16" height="16" fill="none" stroke="#a8e063" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg></div>
                <div>
                    <p style="font-size:.8rem;font-weight:700;color:#1a3828;">Foto y personalización</p>
                    <p style="font-size:.75rem;color:#5a7a65;margin-top:.1rem;">Personaliza el color de tu perfil y carga una foto opcional (JPG, PNG o WebP; máximo 2 MB).</p>
                </div>
            </div>
            <div class="fn-section-body" style="display:flex;align-items:center;gap:1rem;flex-wrap:wrap;">
                <img id="foto-perfil-preview" src="{{ $usuario->profile_photo_path ? route('funcionario.foto') : '' }}" alt="Vista previa de foto de perfil" style="width:76px;height:76px;object-fit:cover;border-radius:50%;border:3px solid var(--profile-accent);{{ $usuario->profile_photo_path ? '' : 'display:none;' }}">
                <span id="foto-perfil-placeholder" style="width:76px;height:76px;display:{{ $usuario->profile_photo_path ? 'none' : 'flex' }};align-items:center;justify-content:center;border-radius:50%;background:var(--profile-accent);color:#fff;font-size:1.5rem;font-weight:700;">{{ strtoupper(substr($funcionario->nombres, 0, 1)) }}</span>
                <label class="fn-label" style="flex:1;min-width:220px;">
                    Foto de perfil
                    <input id="foto-perfil-input" type="file" name="profile_photo" accept="image/jpeg,image/png,image/webp" class="fn-field">
                </label>
                <label class="fn-label" style="min-width:180px;">
                    Color del perfil
                    <select name="profile_color" class="fn-field">
                        @foreach(['#1d6b3d' => 'Verde SENA', '#2563eb' => 'Azul', '#7c3aed' => 'Violeta', '#ea580c' => 'Naranja'] as $color => $nombreColor)
                            <option value="{{ $color }}" @selected(old('profile_color', $usuario->profile_color) === $color)>{{ $nombreColor }}</option>
                        @endforeach
                    </select>
                </label>
            </div>
        </div>

        {{-- Contacto --}}
        <div class="fn-section" style="animation: fadeUp .3s ease both;">
            <div class="fn-section-header">
                <div class="fn-section-icon">
                    <svg width="16" height="16" fill="none" stroke="#a8e063" stroke-width="2" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 1.27h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.6a16 16 0 0 0 5.93 5.93l.92-.92a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 21.73 16z"/></svg>
                </div>
                <div>
                    <p style="font-size:.8rem;font-weight:700;color:#1a3828;">Información de contacto</p>
                    <p style="font-size:.75rem;color:#5a7a65;margin-top:.1rem;">Teléfono, dirección y datos de salud</p>
                </div>
            </div>
            <div class="fn-section-body">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                    <label class="fn-label">
                        Nombres
                        <input type="text" name="nombres" value="{{ old('nombres', $funcionario->nombres) }}" required maxlength="100" class="fn-field">
                    </label>
                    <label class="fn-label">
                        Apellidos
                        <input type="text" name="apellidos" value="{{ old('apellidos', $funcionario->apellidos) }}" required maxlength="100" class="fn-field">
                    </label>
                    <label class="fn-label">
                        Correo de acceso
                        <input type="email" name="email" value="{{ old('email', $funcionario->email) }}" required maxlength="150" class="fn-field">
                    </label>
                    <label class="fn-label">
                        Teléfono / Celular
                        <input type="text" name="telefono" value="{{ old('telefono', $funcionario->telefono) }}" required class="fn-field" placeholder="300 000 0000">
                    </label>
                    <label class="fn-label">
                        Correo personal
                        <input type="email" name="correo_personal" value="{{ old('correo_personal', $funcionario->correo_personal) }}" class="fn-field" placeholder="nombre@correo.com">
                    </label>
                    <label class="fn-label">
                        Correo institucional
                        <input type="email" name="correo_institucional" value="{{ old('correo_institucional', $funcionario->correo_institucional ?? $funcionario->email) }}" class="fn-field" placeholder="nombre@sena.edu.co">
                    </label>
                    <label class="fn-label">
                        Dirección de residencia
                        <input type="text" name="direccion_residencia" value="{{ old('direccion_residencia', $funcionario->direccion_residencia) }}" required class="fn-field" placeholder="Calle 00 # 00-00">
                    </label>
                    <label class="fn-label">
                        EPS
                        <input type="text" name="eps" value="{{ old('eps', $funcionario->eps) }}" required class="fn-field" placeholder="Nombre de la EPS">
                    </label>
                    <label class="fn-label">
                        Fondo de pensiones
                        <input type="text" name="fondo_pension" value="{{ old('fondo_pension', $funcionario->fondo_pension) }}" required class="fn-field" placeholder="Nombre del fondo">
                    </label>
                </div>
            </div>
        </div>

        {{-- Datos académicos y operativos --}}
        <div class="fn-section" style="animation: fadeUp .3s .05s ease both;">
            <div class="fn-section-header">
                <div class="fn-section-icon">
                    <svg width="16" height="16" fill="none" stroke="#a8e063" stroke-width="2" viewBox="0 0 24 24"><path d="m2 10 10-5 10 5-10 5-10-5Z"/><path d="M6 12v5c3 2 9 2 12 0v-5M22 10v6"/></svg>
                </div>
                <div>
                    <p style="font-size:.8rem;font-weight:700;color:#1a3828;">Información académica y operativa</p>
                    <p style="font-size:.75rem;color:#5a7a65;margin-top:.1rem;">Formación, área y red de conocimiento</p>
                </div>
            </div>
            <div class="fn-section-body">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                    <label class="fn-label">Nivel académico
                        <input name="nivel_academico" value="{{ old('nivel_academico', $funcionario->nivel_academico) }}" class="fn-field" maxlength="100" placeholder="Ej. Profesional">
                    </label>
                    <label class="fn-label">Título académico
                        <input name="titulo_academico" value="{{ old('titulo_academico', $funcionario->titulo_academico) }}" class="fn-field" maxlength="150" placeholder="Ej. Ingeniería Industrial">
                    </label>
                    <label class="fn-label">Área
                        <input name="area" value="{{ old('area', $funcionario->area) }}" class="fn-field" maxlength="150" placeholder="Área de trabajo">
                    </label>
                    <label class="fn-label">Red de conocimiento
                        <input name="red_conocimiento" value="{{ old('red_conocimiento', $funcionario->red_conocimiento) }}" class="fn-field" maxlength="150" placeholder="Red de conocimiento">
                    </label>
                </div>
            </div>
        </div>

        {{-- Tallas --}}
        <div class="fn-section" style="animation: fadeUp .3s .07s ease both;">
            <div class="fn-section-header">
                <div class="fn-section-icon">
                    <svg width="16" height="16" fill="none" stroke="#a8e063" stroke-width="2" viewBox="0 0 24 24"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                </div>
                <div>
                    <p style="font-size:.8rem;font-weight:700;color:#1a3828;">Tallas para dotación</p>
                    <p style="font-size:.75rem;color:#5a7a65;margin-top:.1rem;">Obsequios e indumentaria de bienestar</p>
                </div>
            </div>
            <div class="fn-section-body">
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:1.25rem;">
                    {{-- Camisa --}}
                    <div>
                        <p class="fn-label" style="margin-bottom:.625rem;">Talla de camisa</p>
                        <div style="display:flex;gap:.5rem;flex-wrap:wrap;" id="tallas-camisa">
                            @foreach(['XS','S','M','L','XL','XXL'] as $talla)
                            <label>
                                <input type="radio" name="talla_camisa" value="{{ $talla }}" style="display:none;" {{ $funcionario->talla_camisa == $talla ? 'checked' : '' }}>
                                <span class="talla-chip {{ $funcionario->talla_camisa == $talla ? 'selected' : '' }}" onclick="selectTalla(this)">{{ $talla }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    {{-- Pantalón --}}
                    <label class="fn-label">
                        Talla de pantalón
                        <input type="text" name="talla_pantalon" placeholder="Ej. 30, 32" value="{{ old('talla_pantalon', $funcionario->talla_pantalon) }}" required class="fn-field">
                    </label>
                    {{-- Calzado --}}
                    <label class="fn-label">
                        Talla de calzado
                        <input type="text" name="talla_calzado" placeholder="Ej. 38, 40" value="{{ old('talla_calzado', $funcionario->talla_calzado) }}" required class="fn-field">
                    </label>
                </div>
            </div>
        </div>

        {{-- Familiares --}}
        <div class="fn-section" style="animation: fadeUp .3s .14s ease both;">
            <div class="fn-section-header">
                <div class="fn-section-icon">
                    <svg width="16" height="16" fill="none" stroke="#a8e063" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <div style="flex:1;">
                    <p style="font-size:.8rem;font-weight:700;color:#1a3828;">Núcleo familiar</p>
                    <p style="font-size:.75rem;color:#5a7a65;margin-top:.1rem;">Hijos, familiares y personas a tu cargo</p>
                </div>
                <button type="button" id="abrir-familiar" class="btn-add">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                    Agregar
                </button>
            </div>
            <div class="fn-section-body">
                <div id="familiares-lista" style="display:flex;flex-direction:column;gap:.5rem;">
                    @forelse($funcionario->familiares as $indice => $familiar)
                    <div class="familiar-row" data-familiar-row>
                        <span class="familiar-avatar">{{ strtoupper(substr($familiar->nombres, 0, 1)) }}</span>
                        <div style="flex:1;">
                            <span style="font-weight:600;color:#1a3828;">{{ $familiar->nombres }} {{ $familiar->apellidos }}</span>
                            <span style="margin-left:.5rem;font-size:.8rem;color:#5a7a65;">{{ $familiar->parentesco }} · {{ $familiar->fecha_nacimiento->format('d/m/Y') }}</span>
                        </div>
                        <button type="button" style="background:rgba(224,83,83,.08);border:1px solid rgba(224,83,83,.15);color:#e05353;font-size:.75rem;font-weight:600;padding:.35rem .625rem;border-radius:.5rem;cursor:pointer;font-family:'Inter',sans-serif;transition:background .12s;" data-quitar-familiar onmouseover="this.style.background='rgba(224,83,83,.15)'" onmouseout="this.style.background='rgba(224,83,83,.08)'">Quitar</button>
                        <input type="hidden" name="familiares[{{ $indice }}][parentesco]" value="{{ $familiar->parentesco }}">
                        <input type="hidden" name="familiares[{{ $indice }}][nombres]" value="{{ $familiar->nombres }}">
                        <input type="hidden" name="familiares[{{ $indice }}][apellidos]" value="{{ $familiar->apellidos }}">
                        <input type="hidden" name="familiares[{{ $indice }}][tipo_documento]" value="{{ $familiar->tipo_documento }}">
                        <input type="hidden" name="familiares[{{ $indice }}][numero_documento]" value="{{ $familiar->numero_documento }}">
                        <input type="hidden" name="familiares[{{ $indice }}][fecha_nacimiento]" value="{{ $familiar->fecha_nacimiento->format('Y-m-d') }}">
                        <input type="hidden" name="familiares[{{ $indice }}][genero]" value="{{ $familiar->genero }}">
                        <input type="hidden" name="familiares[{{ $indice }}][es_a_cargo]" value="{{ (int) $familiar->es_a_cargo }}">
                    </div>
                    @empty
                    <p id="familiares-vacio" style="text-align:center;color:#8aab98;font-size:.875rem;padding:1.25rem;">Aún no hay familiares registrados.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div style="display:flex;justify-content:flex-end;gap:.75rem;padding-top:.5rem;" class="animate-fade-up-4">
            <a href="{{ route('eventos.index') }}" class="btn-secondary">Cancelar</a>
            <button type="submit" class="btn-submit">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Guardar cambios
            </button>
        </div>
    </form>

    <section class="fn-section" style="margin-top:1.5rem;">
        <div class="fn-section-header">
            <div class="fn-section-icon"><svg width="16" height="16" fill="none" stroke="#a8e063" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></div>
            <div>
                <p style="font-size:.8rem;font-weight:700;color:#1a3828;">Cambiar contraseña</p>
                <p style="font-size:.75rem;color:#5a7a65;margin-top:.1rem;">Confirma tu contraseña actual para establecer una nueva (mínimo 10 caracteres).</p>
            </div>
        </div>
        <div class="fn-section-body">
            <form action="{{ route('funcionario.contrasena.actualizar') }}" method="POST" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:1rem;align-items:end;">
                @csrf
                @method('PUT')
                <label class="fn-label">Contraseña actual<input type="password" name="current_password" required autocomplete="current-password" class="fn-field"></label>
                <label class="fn-label">Nueva contraseña<input type="password" name="password" required minlength="10" autocomplete="new-password" class="fn-field"></label>
                <label class="fn-label">Confirmar contraseña<input type="password" name="password_confirmation" required minlength="10" autocomplete="new-password" class="fn-field"></label>
                <button type="submit" class="btn-submit">Actualizar contraseña</button>
            </form>
        </div>
    </section>
</main>

{{-- Modal familiar --}}
<div id="familiar-modal" class="fn-modal-overlay" style="display:none;" role="dialog" aria-modal="true" aria-labelledby="modal-title">
    <div class="fn-modal">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.375rem;">
            <div>
                <p id="modal-title" style="font-size:1.125rem;font-weight:800;color:#1a3828;">Registrar familiar</p>
                <p style="font-size:.8rem;color:#5a7a65;margin-top:.125rem;">Completa la información del familiar o persona a cargo.</p>
            </div>
            <button type="button" id="cerrar-familiar" style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:.5rem;background:#f2f7f4;border:1px solid #d4e8da;cursor:pointer;font-size:1.125rem;color:#426b53;line-height:1;" aria-label="Cerrar">&times;</button>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.875rem;">
            <label style="display:flex;flex-direction:column;gap:.35rem;font-size:.8rem;font-weight:600;color:#3d7055;">Parentesco<select id="familiar-parentesco" class="fn-field"><option value="HIJO">Hijo/a</option><option value="HIJASTRO">Hijastro/a</option><option value="CONYUGE">Cónyuge</option><option value="OTRO">Otro familiar</option></select></label>
            <label style="display:flex;flex-direction:column;gap:.35rem;font-size:.8rem;font-weight:600;color:#3d7055;">Género<select id="familiar-genero" class="fn-field"><option value="FEMENINO">Mujer</option><option value="MASCULINO">Hombre</option><option value="OTRO">Otro</option></select></label>
            <label style="display:flex;flex-direction:column;gap:.35rem;font-size:.8rem;font-weight:600;color:#3d7055;">Nombres<input id="familiar-nombres" class="fn-field" placeholder="Nombres completos"></label>
            <label style="display:flex;flex-direction:column;gap:.35rem;font-size:.8rem;font-weight:600;color:#3d7055;">Apellidos<input id="familiar-apellidos" class="fn-field" placeholder="Apellidos completos"></label>
            <label style="display:flex;flex-direction:column;gap:.35rem;font-size:.8rem;font-weight:600;color:#3d7055;">Tipo de documento<select id="familiar-tipo-documento" class="fn-field"><option>CC</option><option>TI</option><option>RC</option><option>CE</option></select></label>
            <label style="display:flex;flex-direction:column;gap:.35rem;font-size:.8rem;font-weight:600;color:#3d7055;">Número de documento<input id="familiar-numero-documento" class="fn-field"></label>
            <label style="display:flex;flex-direction:column;gap:.35rem;font-size:.8rem;font-weight:600;color:#3d7055;">Fecha de nacimiento<input id="familiar-fecha-nacimiento" type="date" class="fn-field"></label>
            <label style="display:flex;align-items:center;gap:.625rem;font-size:.875rem;color:#1a3828;cursor:pointer;align-self:end;padding-bottom:.375rem;">
                <input id="familiar-es-a-cargo" type="checkbox" style="width:17px;height:17px;accent-color:#1d6b3d;">
                Está a mi cargo
            </label>
        </div>
        <p id="familiar-error" style="display:none;background:rgba(224,83,83,.08);border:1px solid rgba(224,83,83,.18);color:#e05353;border-radius:.625rem;padding:.625rem .875rem;font-size:.8rem;margin-top:.875rem;">Completa nombres, apellidos y fecha de nacimiento.</p>
        <div style="display:flex;justify-content:flex-end;gap:.625rem;margin-top:1.25rem;">
            <button type="button" id="cancelar-familiar" class="btn-secondary" style="font-size:.875rem;padding:.625rem 1.125rem;">Cancelar</button>
            <button type="button" id="guardar-familiar" class="btn-submit" style="font-size:.875rem;padding:.625rem 1.25rem;">Agregar familiar</button>
        </div>
    </div>
</div>

<script>
// Talla chip selector
function selectTalla(chip) {
    const group = chip.closest('div');
    group.querySelectorAll('.talla-chip').forEach(c => c.classList.remove('selected'));
    chip.classList.add('selected');
    chip.previousElementSibling && (chip.previousElementSibling.checked = true);
}

// Familiar modal
const modal = document.getElementById('familiar-modal');
const lista = document.getElementById('familiares-lista');
let familiarIndex = {{ $funcionario->familiares->count() }};
const campos = ['parentesco','genero','nombres','apellidos','tipo-documento','numero-documento','fecha-nacimiento','es-a-cargo'];
const valor = campo => {
    const el = document.getElementById(`familiar-${campo}`);
    return el.type === 'checkbox' ? el.checked : el.value;
};
const escapar = t => String(t).replace(/[&<>'"]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;',"'": '&#39;','"':'&quot;'}[c]));
const abrirModal = () => { modal.style.display = 'flex'; }
const cerrarModal = () => { modal.style.display = 'none'; }

document.getElementById('abrir-familiar').addEventListener('click', abrirModal);
document.getElementById('cerrar-familiar').addEventListener('click', cerrarModal);
document.getElementById('cancelar-familiar').addEventListener('click', cerrarModal);
modal.addEventListener('click', e => { if (e.target === modal) cerrarModal(); });

document.getElementById('guardar-familiar').addEventListener('click', () => {
    const err = document.getElementById('familiar-error');
    if (!valor('nombres') || !valor('apellidos') || !valor('fecha-nacimiento')) {
        err.style.display = 'block'; return;
    }
    err.style.display = 'none';
    document.getElementById('familiares-vacio')?.remove();

    const initial = (valor('nombres') || '?').charAt(0).toUpperCase();
    const row = document.createElement('div');
    row.className = 'familiar-row';
    row.dataset.familiarRow = '';
    row.innerHTML = `
        <span class="familiar-avatar">${escapar(initial)}</span>
        <div style="flex:1;">
            <span style="font-weight:600;color:#1a3828;">${escapar(valor('nombres'))} ${escapar(valor('apellidos'))}</span>
            <span style="margin-left:.5rem;font-size:.8rem;color:#5a7a65;">${escapar(valor('parentesco'))} · ${escapar(valor('fecha-nacimiento').split('-').reverse().join('/'))}</span>
        </div>
        <button type="button" style="background:rgba(224,83,83,.08);border:1px solid rgba(224,83,83,.15);color:#e05353;font-size:.75rem;font-weight:600;padding:.35rem .625rem;border-radius:.5rem;cursor:pointer;font-family:'Inter',sans-serif;" data-quitar-familiar>Quitar</button>
        ${campos.map(c => `<input type="hidden" name="familiares[${familiarIndex}][${c.replace('-','_')}]" value="${escapar(valor(c))}">`).join('')}
    `;
    row.querySelector('[data-quitar-familiar]').addEventListener('click', () => row.remove());
    lista.appendChild(row);
    familiarIndex++;
    cerrarModal();
    // Reset fields
    campos.forEach(c => {
        const el = document.getElementById(`familiar-${c}`);
        if (el) el.type === 'checkbox' ? el.checked = false : el.value = '';
    });
});

document.querySelectorAll('[data-quitar-familiar]').forEach(btn => btn.addEventListener('click', () => btn.closest('[data-familiar-row]').remove()));

const fotoInput = document.getElementById('foto-perfil-input');
const fotoPreview = document.getElementById('foto-perfil-preview');
const fotoPlaceholder = document.getElementById('foto-perfil-placeholder');
let fotoTemporal = null;

fotoInput.addEventListener('change', () => {
    const archivo = fotoInput.files[0];
    if (!archivo) return;

    if (fotoTemporal) URL.revokeObjectURL(fotoTemporal);
    fotoTemporal = URL.createObjectURL(archivo);
    fotoPreview.src = fotoTemporal;
    fotoPreview.style.display = 'block';
    fotoPlaceholder.style.display = 'none';
});
</script>
</body>
</html>