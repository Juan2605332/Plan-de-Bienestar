<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Bienestar SENA')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap');
        :root { color-scheme: light; }
        body { font-family: Manrope, ui-sans-serif, system-ui, sans-serif; }
        .eyebrow { letter-spacing: .14em; }
        .soft-grid { background-image: linear-gradient(rgba(21, 72, 52, .05) 1px, transparent 1px), linear-gradient(90deg, rgba(21, 72, 52, .05) 1px, transparent 1px); background-size: 28px 28px; }
    </style>
    @stack('head')
</head>
<body class="min-h-screen bg-[#f4f7f3] text-[#17352a] antialiased">
    <div class="min-h-screen">
        <header class="border-b border-[#d9e5dc] bg-[#103c2c] text-white">
            <div class="mx-auto flex max-w-7xl items-center justify-between gap-5 px-5 py-4 lg:px-8">
                <a href="{{ auth()->check() ? route('admin.dashboard') : route('acceso') }}" class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#b8d66f] text-lg font-black text-[#103c2c]">B</span>
                    <span><span class="block text-sm font-semibold tracking-wide">BIENESTAR</span><span class="block text-xs text-[#c7d9ce]">SENA Regional Santander</span></span>
                </a>
                @hasSection('header-actions')
                    <div class="flex items-center gap-3">@yield('header-actions')</div>
                @endif
            </div>
        </header>
        <main class="mx-auto max-w-7xl px-5 py-8 lg:px-8">
            @if (session('success'))<div class="mb-6 rounded-xl border border-[#b9d9c2] bg-[#eaf6ed] px-4 py-3 text-sm text-[#1d6b3d]">{{ session('success') }}</div>@endif
            @if (session('mensaje'))<div class="mb-6 rounded-xl border border-[#e7d39d] bg-[#fff8e4] px-4 py-3 text-sm text-[#7a5b11]">{{ session('mensaje') }}</div>@endif
            @if ($errors->any())<div class="mb-6 rounded-xl border border-[#efc2c2] bg-[#fff0f0] px-4 py-3 text-sm text-[#9a2929]">{{ $errors->first() }}</div>@endif
            @yield('content')
        </main>
        <footer class="mx-auto max-w-7xl px-5 pb-8 text-xs text-[#71847a] lg:px-8">Sistema de gestión de bienestar institucional</footer>
    </div>
    @stack('scripts')
</body>
</html>
