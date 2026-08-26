<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Actualización de Datos - Bienestar SENA</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 p-6">
    <div class="max-w-4xl mx-auto bg-white p-8 rounded-2xl shadow-md border border-slate-200">
        <div class="flex justify-between items-center mb-6 pb-4 border-b">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Actualización de Datos y Tallas</h1>
                <p class="text-sm text-slate-500">Funcionario: {{ $funcionario->nombres }} {{ $funcionario->apellidos }} (CC: {{ $funcionario->cedula }})</p>
            </div>
            <form action="{{ route('salir') }}" method="POST">
                @csrf
                <button type="submit" class="text-xs bg-slate-200 hover:bg-slate-300 text-slate-700 px-3 py-2 rounded-lg">Cerrar Sesión</button>
            </form>
        </div>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('funcionario.guardar') }}" method="POST" class="space-y-6">
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

            <div class="border-t pt-4">
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

            <div class="flex justify-end gap-3 pt-4 border-t">
                <a href="{{ route('eventos.index') }}" class="px-5 py-2.5 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300">Ir a Eventos</a>
                <button type="submit" class="px-5 py-2.5 bg-sky-600 text-white rounded-lg hover:bg-sky-700">Guardar y Continuar</button>
            </div>
        </form>
    </div>
</body>
</html>