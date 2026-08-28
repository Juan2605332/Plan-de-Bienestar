<?php

use App\Models\Evento;
use App\Models\FuncionarioPerfil;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;

uses(RefreshDatabase::class);

it('genera celebraciones y cumpleaños de forma idempotente', function () {
    Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\DatabaseSeeder']);

    Artisan::call('app:generar-eventos-calendario', ['--year' => 2027]);
    Artisan::call('app:generar-eventos-calendario', ['--year' => 2027]);

    expect(Evento::whereHas('periodo', fn ($query) => $query->where('anio', 2027))->count())->toBe(8 + FuncionarioPerfil::where('activo', true)->count());
    expect(Evento::where('nombre', 'Día de la Madre')->whereDate('fecha_evento', '2027-05-09')->exists())->toBeTrue();
    $funcionario = FuncionarioPerfil::where('activo', true)->firstOrFail();
    expect(Evento::where('nombre', 'Cumpleaños: '.$funcionario->nombres.' '.$funcionario->apellidos)->whereMonth('fecha_evento', $funcionario->fecha_nacimiento->month)->whereDay('fecha_evento', $funcionario->fecha_nacimiento->day)->exists())->toBeTrue();
    expect(Evento::where('nombre', 'like', 'Cumpleaños:%')->whereHas('encuestas')->exists())->toBeFalse();
});
