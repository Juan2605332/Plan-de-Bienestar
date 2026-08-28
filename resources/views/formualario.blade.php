<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Actualización de Datos - Bienestar SENA</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-[#f4f7f3] p-4 text-[#17352a] sm:p-6" style="font-family: Manrope, ui-sans-serif, system-ui, sans-serif">
    <div class="mx-auto max-w-5xl overflow-hidden rounded-3xl border border-[#d9e5dc] bg-white shadow-[0_20px_60px_rgba(16,60,44,.1)]">
        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-[#d9e5dc] bg-[#103c2c] px-6 py-5 text-white sm:px-8">
            <div>
                <span class="text-xs font-bold uppercase tracking-[.14em] text-[#b8d66f]">Perfil institucional</span>
                <h1 class="mt-2 text-2xl font-semibold">Actualización de datos</h1>
                <p class="mt-1 text-sm text-[#c7d9ce]">{{ $funcionario->nombres }} {{ $funcionario->apellidos }} · CC {{ $funcionario->cedula }}</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('eventos.index') }}" class="rounded-lg bg-white/10 px-3 py-2 text-xs text-white hover:bg-white/20">Volver a eventos</a>
                <form action="{{ route('salir') }}" method="POST">
                    @csrf
                    <button type="submit" class="rounded-lg bg-white/10 px-3 py-2 text-xs text-white hover:bg-white/20">Cerrar sesión</button>
                </form>
            </div>
        </div>

        <form action="{{ route('funcionario.guardar') }}" method="POST" class="space-y-6 p-6 sm:p-8">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Teléfono / Celular</label>
                    <input type="text" name="telefono" value="{{ old('telefono', $funcionario->telefono) }}" required class="w-full border rounded-lg p-2.5">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Dirección de Residencia</label>
                    <input type="text" name="direccion_residencia" value="{{ old('direccion_residencia', $funcionario->direccion_residencia) }}" required class="w-full border rounded-lg p-2.5">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">EPS</label>
                    <input type="text" name="eps" value="{{ old('eps', $funcionario->eps) }}" required class="w-full border rounded-lg p-2.5">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Fondo de Pensiones</label>
                    <input type="text" name="fondo_pension" value="{{ old('fondo_pension', $funcionario->fondo_pension) }}" required class="w-full border rounded-lg p-2.5">
                </div>
            </div>

            <div class="border-t border-[#d9e5dc] pt-6">
                <h3 class="font-bold text-slate-700 text-sm mb-3">Tallas para Dotación y Obsequios de Bienestar</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs text-slate-600 mb-1">Talla de Camisa</label>
                        <select name="talla_camisa" class="w-full border rounded-lg p-2.5">
                            @foreach(['XS', 'S', 'M', 'L', 'XL', 'XXL'] as $talla)
                                <option value="{{ $talla }}" {{ $funcionario->talla_camisa == $talla ? 'selected' : '' }}>{{ $talla }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-slate-600 mb-1">Talla de Pantalón</label>
                        <input type="text" name="talla_pantalon" placeholder="Ej. 30, 32" value="{{ old('talla_pantalon', $funcionario->talla_pantalon) }}" required class="w-full border rounded-lg p-2.5">
                    </div>
                    <div>
                        <label class="block text-xs text-slate-600 mb-1">Talla de Calzado</label>
                        <input type="text" name="talla_calzado" placeholder="Ej. 38, 40" value="{{ old('talla_calzado', $funcionario->talla_calzado) }}" required class="w-full border rounded-lg p-2.5">
                    </div>
                </div>
            </div>

            <div class="border-t border-[#d9e5dc] pt-6">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h3 class="font-bold text-slate-700 text-sm">Núcleo familiar</h3>
                        <p class="text-xs text-slate-500">Registra hijos, familiares y personas a tu cargo.</p>
                    </div>
                    <button type="button" id="abrir-familiar" class="rounded-lg bg-slate-700 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">Agregar familiar</button>
                </div>
                <div id="familiares-lista" class="mt-4 space-y-2">
                    @forelse($funcionario->familiares as $indice => $familiar)
                        <div class="flex items-center justify-between rounded-lg border border-slate-200 p-3 text-sm" data-familiar-row>
                            <div><span class="font-medium">{{ $familiar->nombres }} {{ $familiar->apellidos }}</span><span class="ml-2 text-slate-500">{{ $familiar->parentesco }} · {{ $familiar->fecha_nacimiento->format('d/m/Y') }}</span></div>
                            <button type="button" class="text-red-600 hover:text-red-700" data-quitar-familiar>Quitar</button>
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
                        <p id="familiares-vacio" class="rounded-lg bg-slate-50 p-3 text-sm text-slate-500">Aún no hay familiares registrados.</p>
                    @endforelse
                </div>
            </div>

            <div class="flex justify-end gap-3 border-t border-[#d9e5dc] pt-5">
                <a href="{{ route('eventos.index') }}" class="px-5 py-2.5 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300">Cancelar</a>
                <button type="submit" class="rounded-xl bg-[#1d6b3d] px-5 py-2.5 font-semibold text-white hover:bg-[#155630]">Guardar cambios</button>
            </div>
        </form>
    </div>

    <div id="familiar-modal" class="fixed inset-0 z-10 hidden items-center justify-center bg-slate-900/50 p-4" role="dialog" aria-modal="true" aria-labelledby="familiar-modal-title">
        <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl">
            <div class="flex items-center justify-between"><h2 id="familiar-modal-title" class="text-lg font-bold text-slate-800">Registrar familiar</h2><button type="button" id="cerrar-familiar" class="text-2xl text-slate-500" aria-label="Cerrar">&times;</button></div>
            <div class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-2">
                <label class="text-sm">Parentesco<select id="familiar-parentesco" class="mt-1 w-full rounded-lg border p-2"><option value="HIJO">Hijo/a</option><option value="HIJASTRO">Hijastro/a</option><option value="CONYUGE">Cónyuge</option><option value="OTRO">Otro familiar</option></select></label>
                <label class="text-sm">Género<select id="familiar-genero" class="mt-1 w-full rounded-lg border p-2"><option value="FEMENINO">Mujer</option><option value="MASCULINO">Hombre</option><option value="OTRO">Otro</option></select></label>
                <label class="text-sm">Nombres<input id="familiar-nombres" class="mt-1 w-full rounded-lg border p-2"></label>
                <label class="text-sm">Apellidos<input id="familiar-apellidos" class="mt-1 w-full rounded-lg border p-2"></label>
                <label class="text-sm">Tipo de documento<select id="familiar-tipo-documento" class="mt-1 w-full rounded-lg border p-2"><option>CC</option><option>TI</option><option>RC</option><option>CE</option></select></label>
                <label class="text-sm">Número de documento<input id="familiar-numero-documento" class="mt-1 w-full rounded-lg border p-2"></label>
                <label class="text-sm">Fecha de nacimiento<input id="familiar-fecha-nacimiento" type="date" class="mt-1 w-full rounded-lg border p-2"></label>
                <label class="flex items-center gap-2 self-end pb-2 text-sm"><input id="familiar-es-a-cargo" type="checkbox" class="rounded"> Está a mi cargo</label>
            </div>
            <p id="familiar-error" class="mt-3 hidden text-sm text-red-600">Completa nombres, apellidos y fecha de nacimiento.</p>
            <div class="mt-5 flex justify-end gap-3"><button type="button" id="cancelar-familiar" class="rounded-xl bg-[#f0f5ef] px-4 py-2 text-[#426b53]">Cancelar</button><button type="button" id="guardar-familiar" class="rounded-xl bg-[#1d6b3d] px-4 py-2 text-white">Agregar</button></div>
        </div>
    </div>
    <script>
        const modal = document.getElementById('familiar-modal');
        const lista = document.getElementById('familiares-lista');
        let familiarIndex = {{ $funcionario->familiares->count() }};
        const campos = ['parentesco', 'genero', 'nombres', 'apellidos', 'tipo-documento', 'numero-documento', 'fecha-nacimiento', 'es-a-cargo'];
        const valor = (campo) => document.getElementById(`familiar-${campo}`).type === 'checkbox' ? document.getElementById(`familiar-${campo}`).checked : document.getElementById(`familiar-${campo}`).value;
        const escapar = (texto) => String(texto).replace(/[&<>'"]/g, caracter => ({'&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;'}[caracter]));
        const cerrarModal = () => modal.classList.replace('flex', 'hidden');
        document.getElementById('abrir-familiar').addEventListener('click', () => modal.classList.replace('hidden', 'flex'));
        document.getElementById('cerrar-familiar').addEventListener('click', cerrarModal);
        document.getElementById('cancelar-familiar').addEventListener('click', cerrarModal);
        document.getElementById('guardar-familiar').addEventListener('click', () => {
            const error = document.getElementById('familiar-error');
            if (!valor('nombres') || !valor('apellidos') || !valor('fecha-nacimiento')) { error.classList.remove('hidden'); return; }
            error.classList.add('hidden');
            document.getElementById('familiares-vacio')?.remove();
            const row = document.createElement('div');
            row.className = 'flex items-center justify-between rounded-lg border border-slate-200 p-3 text-sm';
            row.dataset.familiarRow = '';
            row.innerHTML = `<div><span class="font-medium">${escapar(valor('nombres'))} ${escapar(valor('apellidos'))}</span><span class="ml-2 text-slate-500">${escapar(valor('parentesco'))} · ${escapar(valor('fecha-nacimiento').split('-').reverse().join('/'))}</span></div><button type="button" class="text-red-600" data-quitar-familiar>Quitar</button>${campos.map(campo => `<input type="hidden" name="familiares[${familiarIndex}][${campo.replace('-', '_')}]" value="${escapar(valor(campo))}">`).join('')}`;
            row.querySelector('[data-quitar-familiar]').addEventListener('click', () => row.remove());
            lista.appendChild(row); familiarIndex++; cerrarModal();
        });
        document.querySelectorAll('[data-quitar-familiar]').forEach(button => button.addEventListener('click', () => button.closest('[data-familiar-row]').remove()));
    </script>
</body>
</html>