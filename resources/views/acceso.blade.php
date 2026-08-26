<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienestar Institucional - SENA</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white max-w-md w-full rounded-2xl shadow-lg p-8 border border-slate-200">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Plan de Bienestar</h1>
            <p class="text-sm text-slate-500 mt-1">SENA Regional Santander</p>
        </div>

        @if (session('mensaje'))
            <div class="mb-4 p-3 bg-amber-50 border border-amber-200 text-amber-700 text-sm rounded-lg">
                {{ session('mensaje') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('ingresar') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="cedula" class="block text-sm font-medium text-slate-700 mb-1">Número de Documento</label>
                <input type="text" id="cedula" name="cedula" value="{{ old('cedula') }}" placeholder="Ej. 1098765432" required autofocus
                    class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:ring-2 focus:ring-sky-500 focus:outline-none transition">
            </div>

            <button type="submit" class="w-full bg-sky-600 hover:bg-sky-700 text-white font-medium py-3 rounded-lg transition shadow-md">
                Ingresar al Sistema
            </button>
        </form>
    </div>
</body>
</html>