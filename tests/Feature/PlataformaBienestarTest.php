<?php

use App\Models\Encuesta;
use App\Models\EncuestaRespuesta;
use App\Models\Evento;
use App\Models\EventoDocumento;
use App\Models\FuncionarioEdicionAutorizada;
use App\Models\FuncionarioPerfil;
use App\Models\PeriodoActualizacionDatos;
use App\Models\PeriodoInscripcion;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

test('el perfil queda bloqueado si el período anual está cerrado', function () {
    $this->seed();

    $this->withSession(['funcionario_id' => 1])
        ->get(route('funcionario.formulario'))
        ->assertOk()
        ->assertSee('modo de solo lectura');

    $this->withSession(['funcionario_id' => 1])
        ->post(route('funcionario.guardar'))
        ->assertForbidden();
});

test('el funcionario puede actualizar su información durante el período habilitado', function () {
    $this->seed();
    PeriodoActualizacionDatos::create([
        'nombre' => 'Actualización anual',
        'fecha_inicio' => now()->subHour(),
        'fecha_cierre' => now()->addHour(),
        'activo' => true,
    ]);

    $this->withSession(['funcionario_id' => 1])
        ->post(route('funcionario.guardar'), [
            'correo_personal' => 'persona@example.com',
            'correo_institucional' => 'persona@sena.edu.co',
            'nivel_academico' => 'Profesional',
            'titulo_academico' => 'Ingeniería',
            'area' => 'Tecnología',
            'red_conocimiento' => 'Informática',
            'telefono' => '3001234567',
            'direccion_residencia' => 'Calle 1',
            'eps' => 'SENA EPS',
            'fondo_pension' => 'Colpensiones',
            'talla_camisa' => 'M',
            'talla_pantalon' => '32',
            'talla_calzado' => '39',
            'familiares' => [],
        ])
        ->assertRedirectToRoute('funcionario.formulario')
        ->assertSessionHas('success');

    $this->assertDatabaseHas('funcionarios_perfil', [
        'id' => 1,
        'email' => 'persona@sena.edu.co',
        'correo_personal' => 'persona@example.com',
        'correo_institucional' => 'persona@sena.edu.co',
        'nivel_academico' => 'Profesional',
        'red_conocimiento' => 'Informática',
    ]);
});

test('una autorización administrativa habilita una sola actualización fuera del período', function () {
    $this->seed();
    $administrador = User::factory()->create(['is_admin' => true]);
    $autorizacion = FuncionarioEdicionAutorizada::create([
        'funcionario_id' => 1,
        'administrador_id' => $administrador->id,
        'motivo' => 'Corrección de información',
        'expira_en' => now()->addHour(),
    ]);
    $datos = [
        'telefono' => '3001234567',
        'direccion_residencia' => 'Calle 1',
        'eps' => 'SENA EPS',
        'fondo_pension' => 'Colpensiones',
        'talla_camisa' => 'M',
        'talla_pantalon' => '32',
        'talla_calzado' => '39',
        'familiares' => [],
    ];

    $this->withSession(['funcionario_id' => 1])
        ->post(route('funcionario.guardar'), $datos)
        ->assertRedirectToRoute('funcionario.formulario');

    expect($autorizacion->fresh()->usado_en)->not->toBeNull();

    $this->withSession(['funcionario_id' => 1])
        ->post(route('funcionario.guardar'), $datos)
        ->assertForbidden();
});

test('el administrador configura el período y autoriza una excepción con trazabilidad', function () {
    $this->seed();
    $administrador = User::factory()->create(['is_admin' => true]);

    $this->actingAs($administrador)
        ->get(route('admin.datos.configuracion'))
        ->assertOk()
        ->assertSee('Período de actualización');

    $this->actingAs($administrador)
        ->post(route('admin.datos.periodo.guardar'), [
            'nombre' => 'Actualización anual',
            'fecha_inicio' => now()->addDay()->format('Y-m-d\TH:i'),
            'fecha_cierre' => now()->addDays(10)->format('Y-m-d\TH:i'),
        ])
        ->assertSessionHas('success');

    $this->actingAs($administrador)
        ->post(route('admin.datos.autorizar'), [
            'cedula' => FuncionarioPerfil::findOrFail(1)->cedula,
            'motivo' => 'Corrección solicitada por el funcionario',
            'expira_en' => now()->addHour()->format('Y-m-d\TH:i'),
        ])
        ->assertSessionHas('success');

    $this->assertDatabaseHas('periodos_actualizacion_datos', [
        'nombre' => 'Actualización anual',
        'activo' => true,
        'administrador_id' => $administrador->id,
    ]);
    $this->assertDatabaseHas('funcionario_ediciones_autorizadas', [
        'funcionario_id' => 1,
        'administrador_id' => $administrador->id,
        'motivo' => 'Corrección solicitada por el funcionario',
    ]);
});

test('la inscripción exige y almacena documentos de forma privada', function () {
    $this->seed();
    Storage::fake('local');
    $evento = crearEventoPlataforma(['documentos_requeridos' => ['Copia de cédula']]);

    $this->withSession(['funcionario_id' => 1])
        ->post(route('eventos.inscribir', $evento))
        ->assertSessionHasErrors('documentos.0');

    $this->withSession(['funcionario_id' => 1])
        ->post(route('eventos.inscribir', $evento), [
            'documentos' => [UploadedFile::fake()->create('cedula.pdf', 10, 'application/pdf')],
        ])
        ->assertSessionHas('success');

    $this->assertDatabaseHas('evento_documentos', [
        'tipo_documento' => 'Copia de cédula',
        'nombre_original' => 'cedula.pdf',
    ]);
    $documento = EventoDocumento::firstOrFail();
    Storage::disk('local')->assertExists($documento->ruta);

    $administrador = User::factory()->create(['is_admin' => true]);
    $this->actingAs($administrador)
        ->get(route('admin.documentos.descargar', $documento))
        ->assertDownload('cedula.pdf');
});

test('el QR registra asistencia solo el día del evento y no duplica el registro', function () {
    $this->seed();
    $evento = crearEventoPlataforma(['fecha_evento' => now('America/Bogota')->toDateString()]);
    $this->withSession(['funcionario_id' => 1])->post(route('eventos.inscribir', $evento));
    $inscripcion = $evento->inscripciones()->firstOrFail();
    $this->withSession(['funcionario_id' => 1])
        ->get(route('eventos.index'))
        ->assertOk()
        ->assertSee('Mi QR de asistencia');
    $administrador = User::factory()->create(['is_admin' => true]);

    $this->actingAs($administrador)
        ->get(route('admin.asistencia.confirmar', $inscripcion->qr_token))
        ->assertOk()
        ->assertSee('Confirmar asistencia');

    $this->actingAs($administrador)
        ->get(route('admin.inscritos', $evento))
        ->assertOk()
        ->assertSee('Mostrar QR');

    $this->actingAs($administrador)
        ->post(route('admin.asistencia.registrar', $inscripcion->qr_token))
        ->assertRedirectToRoute('admin.asistencia.confirmar', $inscripcion->qr_token)
        ->assertSessionHas('success');

    $horaRegistro = $inscripcion->asistencia()->firstOrFail()->hora_registro;

    $this->actingAs($administrador)
        ->post(route('admin.asistencia.registrar', $inscripcion->qr_token))
        ->assertSessionHas('success');

    expect($inscripcion->asistencia()->count())->toBe(1)
        ->and($inscripcion->asistencia()->firstOrFail()->hora_registro->equalTo($horaRegistro))->toBeTrue();
});

test('los reportes presentan participación por centro y exportan un CSV', function () {
    $this->seed();
    $evento = crearEventoPlataforma(['fecha_evento' => Carbon::now()->toDateString()]);
    $inscripcion = $evento->inscripciones()->create([
        'funcionario_id' => 1,
        'qr_token' => (string) Str::uuid(),
    ]);
    $inscripcion->asistencia()->create(['asistio' => true, 'hora_registro' => now()]);
    $encuesta = Encuesta::create(['evento_id' => $evento->id, 'titulo' => 'Satisfacción', 'activa' => true]);
    $pregunta = $encuesta->preguntas()->create([
        'enunciado' => 'Califica la actividad',
        'tipo_pregunta' => 'ESCALA_1_5',
        'orden' => 1,
        'es_obligatoria' => true,
    ]);
    $opcion = $pregunta->opciones()->create(['texto_opcion' => '5', 'valor_numerico' => 5]);
    EncuestaRespuesta::create([
        'pregunta_id' => $pregunta->id,
        'funcionario_id' => 1,
        'opcion_id' => $opcion->id,
        'respuesta_numero' => 5,
    ]);
    $administrador = User::factory()->create(['is_admin' => true]);

    $this->actingAs($administrador)
        ->get(route('admin.reportes', ['anio' => now()->year]))
        ->assertOk()
        ->assertSee($evento->nombre)
        ->assertSee('5.00')
        ->assertSee('100.00%');

    $this->actingAs($administrador)
        ->get(route('admin.reportes.exportar', ['anio' => now()->year]))
        ->assertOk()
        ->assertHeader('content-type', 'text/csv; charset=UTF-8')
        ->assertHeaderContains('content-disposition', 'reporte-bienestar-'.now()->year.'.csv');
});

test('el calendario anual es visible para funcionarios autenticados', function () {
    $this->seed();
    $evento = crearEventoPlataforma(['nombre' => 'Día de la Familia']);

    $this->withSession(['funcionario_id' => 1])
        ->get(route('eventos.calendario'))
        ->assertOk()
        ->assertSee('Calendario de actividades')
        ->assertSee($evento->nombre);
});

function crearEventoPlataforma(array $atributos = []): Evento
{
    $periodo = PeriodoInscripcion::create([
        'nombre' => 'Inscripciones vigentes',
        'anio' => (int) now()->year,
        'fecha_inicio' => now()->subDay(),
        'fecha_cierre' => now()->addMonth(),
        'activo' => true,
    ]);

    return Evento::create(array_merge([
        'periodo_id' => $periodo->id,
        'nombre' => 'Jornada de salud',
        'descripcion' => 'Actividad de bienestar',
        'fecha_evento' => now()->addWeek()->toDateString(),
        'lugar' => 'Centro de formación',
        'dirigido_a_genero' => 'TODOS',
        'requiere_ser_padre_madre' => false,
        'requiere_familiar_a_cargo' => false,
        'estado' => 'PROGRAMADO',
    ], $atributos));
}
