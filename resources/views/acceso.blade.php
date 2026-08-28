@extends('layouts.app')

@section('title', 'Acceso | Bienestar SENA')

@section('content')
<div class="soft-grid flex min-h-[calc(100vh-150px)] items-center justify-center rounded-3xl px-4 py-12">
    <div class="grid w-full max-w-5xl overflow-hidden rounded-3xl border border-[#d9e5dc] bg-white shadow-[0_24px_70px_rgba(16,60,44,.12)] lg:grid-cols-[1.1fr_.9fr]">
        <section class="relative overflow-hidden bg-[#103c2c] px-8 py-12 text-white lg:px-12 lg:py-16">
            <div class="absolute -right-16 -top-16 h-56 w-56 rounded-full border-[28px] border-[#426b53]/60"></div>
            <div class="relative max-w-md"><span class="eyebrow text-xs font-bold uppercase text-[#b8d66f]">Portal institucional</span><h1 class="mt-6 text-4xl font-semibold leading-tight lg:text-5xl">Bienestar que se convierte en comunidad.</h1><p class="mt-6 max-w-sm text-base leading-7 text-[#c7d9ce]">Gestiona tus datos, descubre actividades y participa en las iniciativas de Bienestar SENA.</p><div class="mt-12 grid grid-cols-2 gap-3 text-sm"><div class="border-l-2 border-[#b8d66f] pl-3"><strong class="block text-2xl text-white">24/7</strong><span class="text-[#c7d9ce]">Acceso digital</span></div><div class="border-l-2 border-[#b8d66f] pl-3"><strong class="block text-2xl text-white">SENA</strong><span class="text-[#c7d9ce]">Regional Santander</span></div></div></div>
        </section>
        <section class="px-8 py-10 lg:px-12 lg:py-16"><span class="eyebrow text-xs font-bold uppercase text-[#648273]">Ingreso seguro</span><h2 class="mt-3 text-3xl font-semibold text-[#17352a]">Bienvenido de nuevo</h2><p class="mt-2 text-sm text-[#71847a]">Usa tus credenciales institucionales para continuar.</p><form action="{{ route('ingresar') }}" method="POST" class="mt-8 space-y-5">@csrf<label class="block text-sm font-medium">Usuario<input type="email" name="usuario" value="{{ old('usuario') }}" placeholder="admin@sena.edu.co" required autofocus class="mt-2 w-full rounded-xl border border-[#cddbd1] bg-[#fbfdfb] px-4 py-3.5 outline-none transition focus:border-[#23734d] focus:ring-4 focus:ring-[#b8d66f]/30"></label><label class="block text-sm font-medium">Contraseña<input type="password" name="password" required class="mt-2 w-full rounded-xl border border-[#cddbd1] bg-[#fbfdfb] px-4 py-3.5 outline-none transition focus:border-[#23734d] focus:ring-4 focus:ring-[#b8d66f]/30"></label><div class="flex items-center justify-between pt-3"><button type="reset" class="rounded-xl px-4 py-3 text-sm font-semibold text-[#71847a] transition hover:bg-[#f0f5ef]">Limpiar</button><button type="submit" class="rounded-xl bg-[#1d6b3d] px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-[#1d6b3d]/20 transition hover:bg-[#155630]">Ingresar al sistema</button></div></form></section>
    </div>
</div>
@endsection
