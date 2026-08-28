<?php

use App\Models\Evento;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;

uses(RefreshDatabase::class);

it('genera celebraciones y cumpleaños de forma idempotente', function () {
    Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\DatabaseSeeder']);

    Artisan::call('app:generar-eventos-calendario', ['--year' => 2027]);
    Artisan::call('app:generar-eventos-calendario', ['--year' => 2027]);

    expect(Evento::whereHas('periodo', fn ($query) => $query->where('anio', 2027))->count())->toBe(14);
    expect(Evento::where('nombre', 'Día de la Madre')->whereDate('fecha_evento', '2027-05-09')->exists())->toBeTrue();
    expect(Evento::where('nombre', 'Cumpleaños: Funcionario de Prueba')->whereDate('fecha_evento', '2027-05-15')->exists())->toBeTrue();
    expect(Evento::where('nombre', 'like', 'Cumpleaños:%')->whereHas('encuestas')->exists())->toBeFalse();
});
