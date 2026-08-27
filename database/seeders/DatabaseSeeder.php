<?php

namespace Database\Seeders;

use App\Models\BeneficiarioFamiliar;
use App\Models\CentroFormacion;
use App\Models\Encuesta;
use App\Models\EncuestaRespuesta;
use App\Models\Evento;
use App\Models\EventoAsistencia;
use App\Models\EventoInscripcion;
use App\Models\FuncionarioPerfil;
use App\Models\PeriodoInscripcion;
use App\Models\TipoCargo;
use App\Models\TipoVinculacion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $this->seedReferenceData();

            $funcionarios = $this->seedFuncionarios();
            $periodos = $this->seedPeriodos();
            $eventos = $this->seedEventos($periodos);

            $this->seedInscripciones($funcionarios, $eventos);
            $this->seedEncuestas($funcionarios, $eventos);
        });
    }

    private function seedReferenceData(): void
    {
        foreach (['ADMINISTRATIVO', 'INSTRUCTOR', 'TRABAJADOR OFICIAL', 'PROFESIONAL'] as $nombre) {
            TipoCargo::firstOrCreate(['nombre' => $nombre]);
        }

        foreach (['CARRERA ADMINISTRATIVA', 'PROVISIONAL', 'LIBRE NOMBRAMIENTO Y REMOCIÓN', 'CONTRATISTA (APOYO)'] as $nombre) {
            TipoVinculacion::firstOrCreate(['nombre' => $nombre]);
        }

        foreach ([
            ['nombre' => 'CENTRO INDUSTRIAL DE MANTENIMIENTO INTEGRAL (CIMI)', 'municipio' => 'Girón'],
            ['nombre' => 'CENTRO DE SERVICIOS EMPRESARIALES Y TURÍSTICOS (CSET)', 'municipio' => 'Bucaramanga'],
            ['nombre' => 'CENTRO DE ATENCIÓN AL SECTOR AGROPECUARIO (CASA)', 'municipio' => 'Piedecuesta'],
            ['nombre' => 'CENTRO AGROTURÍSTICO', 'municipio' => 'San Gil'],
            ['nombre' => 'CENTRO AGROEMPRESARIAL Y TURÍSTICO DE LOS ANDES', 'municipio' => 'Málaga'],
            ['nombre' => 'SEDE REGIONAL SANTANDER - DIRECCIÓN', 'municipio' => 'Bucaramanga'],
        ] as $centro) {
            CentroFormacion::firstOrCreate(['nombre' => $centro['nombre']], $centro);
        }
    }

    /**
     * @return array<int, FuncionarioPerfil>
     */
    private function seedFuncionarios(): array
    {
        $faker = fake('es_ES');
        $cargoIds = TipoCargo::pluck('id')->all();
        $vinculacionIds = TipoVinculacion::pluck('id')->all();
        $centroIds = CentroFormacion::pluck('id')->all();
        $funcionarios = [];

        for ($indice = 0; $indice < 12; $indice++) {
            $cedula = (string) (1098765432 + $indice);
            $funcionario = FuncionarioPerfil::updateOrCreate(
                ['cedula' => $cedula],
                [
                    'tipo_cargo_id' => $cargoIds[$indice % count($cargoIds)],
                    'tipo_vinculacion_id' => $vinculacionIds[$indice % count($vinculacionIds)],
                    'centro_formacion_id' => $centroIds[$indice % count($centroIds)],
                    'nombres' => $indice === 0 ? 'Funcionario' : $faker->firstName(),
                    'apellidos' => $indice === 0 ? 'de Prueba' : $faker->lastName().' '.$faker->lastName(),
                    'genero' => $indice % 3 === 1 ? 'FEMENINO' : 'MASCULINO',
                    'fecha_nacimiento' => $faker->dateTimeBetween('-60 years', '-22 years')->format('Y-m-d'),
                    'email' => "funcionario{$indice}@sena.edu.co",
                    'telefono' => $faker->numerify('3#########'),
                    'direccion_residencia' => $faker->address(),
                    'eps' => $faker->randomElement(['Sanitas', 'Sura', 'Nueva EPS']),
                    'fondo_pension' => $faker->randomElement(['Porvenir', 'Protección', 'Colpensiones']),
                    'talla_camisa' => $faker->randomElement(['S', 'M', 'L', 'XL']),
                    'talla_pantalon' => (string) $faker->randomElement([28, 30, 32, 34, 36]),
                    'talla_calzado' => (string) $faker->numberBetween(36, 44),
                    'es_padre_madre' => $indice % 3 === 0,
                    'activo' => $indice !== 11,
                ],
            );

            if ($funcionario->es_padre_madre) {
                BeneficiarioFamiliar::updateOrCreate(
                    ['numero_documento' => "FAM{$indice}001"],
                    [
                        'funcionario_id' => $funcionario->id,
                        'parentesco' => 'HIJO',
                        'nombres' => $faker->firstName(),
                        'apellidos' => $funcionario->apellidos,
                        'tipo_documento' => 'TI',
                        'fecha_nacimiento' => $faker->dateTimeBetween('-17 years', '-2 years')->format('Y-m-d'),
                        'genero' => $faker->randomElement(['MASCULINO', 'FEMENINO']),
                    ],
                );
            }

            $funcionarios[] = $funcionario;
        }

        return $funcionarios;
    }

    /**
     * @return array<string, PeriodoInscripcion>
     */
    private function seedPeriodos(): array
    {
        return [
            'vigente' => PeriodoInscripcion::updateOrCreate(
                ['nombre' => 'Periodo de bienestar vigente'],
                [
                    'anio' => now()->year,
                    'fecha_inicio' => now()->subMonth(),
                    'fecha_cierre' => now()->addMonths(3),
                    'activo' => true,
                ],
            ),
            'cerrado' => PeriodoInscripcion::updateOrCreate(
                ['nombre' => 'Periodo de bienestar cerrado'],
                [
                    'anio' => now()->subYear()->year,
                    'fecha_inicio' => now()->subYear()->startOfYear(),
                    'fecha_cierre' => now()->subYear()->endOfYear(),
                    'activo' => true,
                ],
            ),
        ];
    }

    /**
     * @param  array<string, PeriodoInscripcion>  $periodos
     * @return array<string, Evento>
     */
    private function seedEventos(array $periodos): array
    {
        $definiciones = [
            'general' => ['Jornada de salud integral', 'TODOS', false, 'PROGRAMADO', null, $periodos['vigente']],
            'familiar' => ['Encuentro para familias SENA', 'TODOS', true, 'PROGRAMADO', 5, $periodos['vigente']],
            'femenino' => ['Espacio de bienestar para mujeres', 'FEMENINO', false, 'PROGRAMADO', 20, $periodos['vigente']],
            'finalizado' => ['Taller de hábitos saludables', 'TODOS', false, 'FINALIZADO', null, $periodos['vigente']],
            'cerrado' => ['Actividad de periodo cerrado', 'TODOS', false, 'PROGRAMADO', null, $periodos['cerrado']],
        ];

        $eventos = [];
        foreach ($definiciones as $clave => [$nombre, $genero, $requiereFamilia, $estado, $cupo, $periodo]) {
            $eventos[$clave] = Evento::updateOrCreate(
                ['nombre' => $nombre],
                [
                    'periodo_id' => $periodo->id,
                    'descripcion' => 'Datos de demostración para validar el flujo de eventos.',
                    'fecha_evento' => now()->addWeeks($estado === 'FINALIZADO' ? -2 : 2),
                    'lugar' => 'SENA Regional Santander',
                    'cupo_maximo' => $cupo,
                    'dirigido_a_genero' => $genero,
                    'requiere_ser_padre_madre' => $requiereFamilia,
                    'estado' => $estado,
                ],
            );
        }

        return $eventos;
    }

    /**
     * @param  array<int, FuncionarioPerfil>  $funcionarios
     * @param  array<string, Evento>  $eventos
     */
    private function seedInscripciones(array $funcionarios, array $eventos): void
    {
        foreach (array_slice($funcionarios, 0, 6) as $indice => $funcionario) {
            $evento = $indice % 2 === 0 ? $eventos['general'] : $eventos['familiar'];
            $inscripcion = EventoInscripcion::updateOrCreate(
                ['evento_id' => $evento->id, 'funcionario_id' => $funcionario->id],
                ['estado' => 'INSCRITO', 'fecha_inscripcion' => now()->subDays($indice)],
            );

            if ($indice < 2) {
                EventoAsistencia::updateOrCreate(
                    ['inscripcion_id' => $inscripcion->id],
                    ['asistio' => $indice === 0, 'hora_registro' => $indice === 0 ? now()->subDay() : null],
                );
            }
        }
    }

    /**
     * @param  array<int, FuncionarioPerfil>  $funcionarios
     * @param  array<string, Evento>  $eventos
     */
    private function seedEncuestas(array $funcionarios, array $eventos): void
    {
        $encuesta = Encuesta::updateOrCreate(
            ['evento_id' => $eventos['general']->id, 'titulo' => 'Evaluación de la jornada'],
            [
                'instrucciones' => 'Responde esta encuesta para ayudarnos a mejorar las actividades.',
                'activa' => true,
                'fecha_limite_respuesta' => now()->addMonth(),
            ],
        );

        $preguntaEscala = $encuesta->preguntas()->updateOrCreate(
            ['orden' => 1],
            ['enunciado' => '¿Cómo calificas la actividad?', 'tipo_pregunta' => 'ESCALA_1_5', 'es_obligatoria' => true],
        );
        $preguntaAbierta = $encuesta->preguntas()->updateOrCreate(
            ['orden' => 2],
            ['enunciado' => '¿Qué mejorarías?', 'tipo_pregunta' => 'ABIERTA', 'es_obligatoria' => false],
        );

        foreach (range(1, 5) as $valor) {
            $preguntaEscala->opciones()->updateOrCreate(
                ['texto_opcion' => (string) $valor],
                ['valor_numerico' => $valor],
            );
        }

        foreach (array_slice($funcionarios, 0, 3) as $funcionario) {
            $inscripcion = EventoInscripcion::where('evento_id', $eventos['general']->id)
                ->where('funcionario_id', $funcionario->id)
                ->first();

            if ($inscripcion === null) {
                continue;
            }

            $opcion = $preguntaEscala->opciones()->inRandomOrder()->first();
            EncuestaRespuesta::updateOrCreate(
                ['pregunta_id' => $preguntaEscala->id, 'funcionario_id' => $funcionario->id],
                ['opcion_id' => $opcion->id, 'respuesta_numero' => $opcion->valor_numerico],
            );
            EncuestaRespuesta::updateOrCreate(
                ['pregunta_id' => $preguntaAbierta->id, 'funcionario_id' => $funcionario->id],
                ['respuesta_texto' => 'La actividad fue útil y bien organizada.'],
            );
        }
    }
}
