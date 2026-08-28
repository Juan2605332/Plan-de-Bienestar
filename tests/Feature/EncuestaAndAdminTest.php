<?php

use App\Imports\FuncionariosImport;
use App\Models\Encuesta;
use App\Models\EncuestaPregunta;
use App\Models\Evento;
use App\Models\FuncionarioPerfil;
use App\Models\PeriodoInscripcion;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

test('un funcionario inscrito puede responder una encuesta y actualizar su respuesta', function () {
    $this->seed();
    $evento = crearEventoParaEncuesta();
    $encuesta = Encuesta::create([
        'evento_id' => $evento->id,
        'titulo' => 'Satisfacción',
        'activa' => true,
    ]);
    $pregunta = EncuestaPregunta::create([
        'encuesta_id' => $encuesta->id,
        'enunciado' => '¿Cómo calificas la actividad?',
        'tipo_pregunta' => 'MULTIPLE_UNICA',
        'orden' => 1,
        'es_obligatoria' => true,
    ]);
    $opciones = $pregunta->opciones()->createMany([
        ['texto_opcion' => 'Excelente', 'valor_numerico' => 5],
        ['texto_opcion' => 'Buena', 'valor_numerico' => 4],
    ]);

    $this->withSession(['funcionario_id' => 1])
        ->post(route('eventos.inscribir', $evento));

    $this->withSession(['funcionario_id' => 1])
        ->get(route('encuestas.mostrar', $encuesta))
        ->assertSee($pregunta->enunciado);

    $this->withSession(['funcionario_id' => 1])
        ->post(route('encuestas.responder', $encuesta), [
            'respuestas' => [$pregunta->id => $opciones[0]->id],
        ])
        ->assertRedirectToRoute('eventos.index');

    $this->assertDatabaseHas('encuesta_respuestas', [
        'pregunta_id' => $pregunta->id,
        'funcionario_id' => 1,
        'opcion_id' => $opciones[0]->id,
        'respuesta_numero' => 5,
    ]);

    $this->withSession(['funcionario_id' => 1])
        ->post(route('encuestas.responder', $encuesta), [
            'respuestas' => [$pregunta->id => $opciones[1]->id],
        ]);

    expect($pregunta->respuestas()->count())->toBe(1);
    $this->assertDatabaseHas('encuesta_respuestas', [
        'pregunta_id' => $pregunta->id,
        'funcionario_id' => 1,
        'opcion_id' => $opciones[1]->id,
    ]);
});

test('un funcionario no inscrito no puede consultar ni responder una encuesta', function () {
    $this->seed();
    $evento = crearEventoParaEncuesta();
    $encuesta = Encuesta::create([
        'evento_id' => $evento->id,
        'titulo' => 'Satisfacción',
        'activa' => true,
    ]);

    $this->withSession(['funcionario_id' => 1])
        ->get(route('encuestas.mostrar', $encuesta))
        ->assertForbidden();
});

test('una encuesta exige sus preguntas obligatorias', function () {
    $this->seed();
    $evento = crearEventoParaEncuesta();
    $encuesta = Encuesta::create([
        'evento_id' => $evento->id,
        'titulo' => 'Satisfacción',
        'activa' => true,
    ]);
    $pregunta = $encuesta->preguntas()->create([
        'enunciado' => 'Pregunta obligatoria',
        'tipo_pregunta' => 'ABIERTA',
        'orden' => 1,
        'es_obligatoria' => true,
    ]);
    $evento->inscripciones()->create(['funcionario_id' => 1]);

    $this->withSession(['funcionario_id' => 1])
        ->post(route('encuestas.responder', $encuesta), ['respuestas' => []])
        ->assertUnprocessable()
        ->assertSee('Responde todas las preguntas obligatorias.');

    $this->assertDatabaseMissing('encuesta_respuestas', ['pregunta_id' => $pregunta->id]);
});

test('el administrador puede crear cualquier cantidad de preguntas con tipos y opciones diferentes', function () {
    $this->seed();
    $evento = crearEventoParaEncuesta();
    $administrador = User::factory()->create(['is_admin' => true]);

    $this->actingAs($administrador)
        ->get(route('admin.encuestas.crear', $evento))
        ->assertOk()
        ->assertSee('Agregar pregunta')
        ->assertSee('Selección múltiple');

    $this->actingAs($administrador)
        ->post(route('admin.encuestas.guardar', $evento), [
            'titulo' => 'Perfil de participación',
            'preguntas' => [
                ['enunciado' => 'Cuéntanos tu opinión', 'tipo_pregunta' => 'ABIERTA', 'es_obligatoria' => '1'],
                ['enunciado' => 'Selecciona tus intereses', 'tipo_pregunta' => 'MULTIPLE', 'opciones' => "Deporte\nCultura\nFamilia", 'es_obligatoria' => '1'],
                ['enunciado' => '¿Cuál es tu género?', 'tipo_pregunta' => 'GENERO', 'es_obligatoria' => '1'],
                ['enunciado' => '¿Tienes hijos?', 'tipo_pregunta' => 'HIJOS', 'es_obligatoria' => '0'],
            ],
        ])
        ->assertRedirectToRoute('admin.dashboard');

    $encuesta = Encuesta::where('titulo', 'Perfil de participación')->firstOrFail();

    expect($encuesta->preguntas)->toHaveCount(4);
    expect($encuesta->preguntas->where('tipo_pregunta', 'MULTIPLE')->first()->opciones)->toHaveCount(3);
    expect($encuesta->preguntas->where('tipo_pregunta', 'GENERO')->first()->opciones)->toHaveCount(3);
});

test('solo un usuario administrador puede acceder al panel administrativo', function () {
    $usuario = User::factory()->create(['is_admin' => false]);
    $administrador = User::factory()->create(['is_admin' => true]);

    $this->actingAs($usuario)
        ->get(route('admin.dashboard'))
        ->assertForbidden();

    $this->actingAs($administrador)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('Panel administrativo');
});

test('un administrador puede buscar eventos por nombre, descripción o lugar', function () {
    $this->seed();
    $administrador = User::factory()->create(['is_admin' => true]);

    $this->actingAs($administrador)
        ->get(route('admin.dashboard', ['buscar_evento' => 'mujeres']))
        ->assertSee('Espacio de bienestar para mujeres');

    $this->actingAs($administrador)
        ->get(route('admin.dashboard', ['buscar_evento' => 'no existe']))
        ->assertDontSee('Espacio de bienestar para mujeres');
});

test('la importación crea funcionarios y exige un archivo en el panel', function () {
    $this->seed();

    (new FuncionariosImport)->collection(new Collection([
        [
            'cedula' => '1234567890',
            'tipo_cargo' => 'NUEVO CARGO',
            'tipo_vinculacion' => 'NUEVA VINCULACION',
            'centro_formacion' => 'NUEVO CENTRO',
            'municipio' => 'Bucaramanga',
            'nombres' => 'Ana',
            'apellidos' => 'Pérez',
            'genero' => 'femenino',
            'fecha_nacimiento' => '1992-01-10',
            'email' => 'ana@sena.edu.co',
        ],
    ]));

    $funcionario = FuncionarioPerfil::where('cedula', '1234567890')->firstOrFail();

    expect($funcionario->nombres)->toBe('Ana');
    expect($funcionario->genero)->toBe('FEMENINO');
    $this->assertDatabaseHas('tipos_cargo', ['nombre' => 'NUEVO CARGO']);

    $administrador = User::factory()->create(['is_admin' => true]);

    $this->actingAs($administrador)
        ->post(route('admin.funcionarios.importar.guardar'))
        ->assertSessionHasErrors('archivo');
});

function crearEventoParaEncuesta(): Evento
{
    $periodo = PeriodoInscripcion::create([
        'nombre' => 'Periodo encuesta',
        'anio' => (int) now()->year,
        'fecha_inicio' => Carbon::now()->subDay(),
        'fecha_cierre' => Carbon::now()->addMonth(),
        'activo' => true,
    ]);

    return Evento::create([
        'periodo_id' => $periodo->id,
        'nombre' => 'Actividad de encuesta',
        'fecha_evento' => now()->addWeek(),
        'dirigido_a_genero' => 'TODOS',
        'requiere_ser_padre_madre' => false,
        'estado' => 'PROGRAMADO',
    ]);
}
