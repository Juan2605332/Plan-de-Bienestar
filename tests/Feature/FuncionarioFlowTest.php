<?php

use App\Models\Evento;
use App\Models\PeriodoInscripcion;
use Illuminate\Support\Carbon;

test('una persona no autenticada es redirigida al acceso', function () {
    $this->get('/eventos')
        ->assertRedirectToRoute('acceso');
});

test('un funcionario activo puede iniciar sesión con su cédula', function () {
    $this->seed();

    $this->post('/ingresar', ['cedula' => '1098765432'])
        ->assertRedirectToRoute('funcionario.formulario')
        ->assertSessionHas('funcionario_id', 1);
});

test('un funcionario no puede inscribirse en un evento de otro género', function () {
    $this->seed();
    $evento = crearEvento(['dirigido_a_genero' => 'FEMENINO']);

    $this->withSession(['funcionario_id' => 1])
        ->post(route('eventos.inscribir', $evento))
        ->assertSessionHas('mensaje', 'No cumples las condiciones para inscribirte en este evento.');

    $this->assertDatabaseMissing('evento_inscripciones', [
        'evento_id' => $evento->id,
        'funcionario_id' => 1,
    ]);
});

test('un funcionario puede inscribirse una sola vez y el cupo se respeta', function () {
    $this->seed();
    $evento = crearEvento(['cupo_maximo' => 1]);

    $this->withSession(['funcionario_id' => 1])
        ->post(route('eventos.inscribir', $evento))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('evento_inscripciones', [
        'evento_id' => $evento->id,
        'funcionario_id' => 1,
        'estado' => 'INSCRITO',
    ]);

    $this->withSession(['funcionario_id' => 1])
        ->post(route('eventos.inscribir', $evento))
        ->assertSessionHas('mensaje', 'Ya estás inscrito en este evento.');

    expect($evento->inscripciones()->count())->toBe(1);
});

test('los eventos fuera del periodo vigente no aparecen ni aceptan inscripciones', function () {
    $this->seed();
    $evento = crearEvento([
        'periodo_id' => PeriodoInscripcion::create([
            'nombre' => 'Periodo cerrado',
            'anio' => 2025,
            'fecha_inicio' => Carbon::now()->subYear(),
            'fecha_cierre' => Carbon::now()->subMonth(),
            'activo' => true,
        ])->id,
    ]);

    $this->withSession(['funcionario_id' => 1])
        ->get(route('eventos.index'))
        ->assertDontSee($evento->nombre);

    $this->withSession(['funcionario_id' => 1])
        ->post(route('eventos.inscribir', $evento))
        ->assertSessionHas('mensaje', 'No cumples las condiciones para inscribirte en este evento.');
});

function crearEvento(array $attributes = []): Evento
{
    $periodo = PeriodoInscripcion::create([
        'nombre' => 'Periodo vigente',
        'anio' => (int) now()->year,
        'fecha_inicio' => Carbon::now()->subDay(),
        'fecha_cierre' => Carbon::now()->addMonth(),
        'activo' => true,
    ]);

    return Evento::create(array_merge([
        'periodo_id' => $periodo->id,
        'nombre' => 'Jornada de bienestar',
        'descripcion' => 'Actividad de prueba',
        'fecha_evento' => now()->addWeek(),
        'lugar' => 'CIMI',
        'cupo_maximo' => null,
        'dirigido_a_genero' => 'TODOS',
        'requiere_ser_padre_madre' => false,
        'estado' => 'PROGRAMADO',
    ], $attributes));
}
