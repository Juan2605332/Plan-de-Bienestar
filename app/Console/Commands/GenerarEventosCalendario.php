<?php

namespace App\Console\Commands;

use App\Models\Evento;
use App\Models\FuncionarioPerfil;
use App\Models\PeriodoInscripcion;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:generar-eventos-calendario {--year= : Año que se desea generar}')]
#[Description('Genera cumpleaños y celebraciones anuales del calendario de bienestar')]
class GenerarEventosCalendario extends Command
{
    public function handle(): int
    {
        $year = (int) ($this->option('year') ?: now('America/Bogota')->year);
        $periodo = PeriodoInscripcion::updateOrCreate(
            ['nombre' => 'Calendario de Bienestar '.$year],
            ['anio' => $year, 'fecha_inicio' => $year.'-01-01 00:00:00', 'fecha_cierre' => $year.'-12-31 23:59:59', 'activo' => true],
        );
        $total = 0;

        foreach ($this->celebraciones($year) as $celebracion) {
            $evento = Evento::updateOrCreate(
                ['periodo_id' => $periodo->id, 'nombre' => $celebracion['nombre']],
                $celebracion + ['estado' => 'PROGRAMADO'],
            );
            $evento->encuestas()->delete();
            $total++;
        }

        FuncionarioPerfil::query()->where('activo', true)->each(function (FuncionarioPerfil $funcionario) use ($year, $periodo, &$total): void {
            $fecha = CarbonImmutable::create($year, $funcionario->fecha_nacimiento->month, $funcionario->fecha_nacimiento->day);

            if ($funcionario->fecha_nacimiento->month === 2 && $funcionario->fecha_nacimiento->day === 29 && ! $fecha->isLeapYear()) {
                $fecha = $fecha->subDay();
            }

            Evento::updateOrCreate(
                ['periodo_id' => $periodo->id, 'nombre' => 'Cumpleaños: '.$funcionario->nombres.' '.$funcionario->apellidos],
                [
                    'descripcion' => 'Celebración de cumpleaños de '.$funcionario->nombres.' '.$funcionario->apellidos.'.',
                    'fecha_evento' => $fecha->toDateString(),
                    'dirigido_a_genero' => 'TODOS',
                    'requiere_ser_padre_madre' => false,
                    'estado' => 'PROGRAMADO',
                ],
            );
            $total++;
        });

        $this->info("Se verificaron {$total} eventos para {$year}.");

        return self::SUCCESS;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function celebraciones(int $year): array
    {
        return [
            $this->celebracion('Día Internacional de la Mujer', CarbonImmutable::create($year, 3, 8), 'Reconocimiento y bienestar para las mujeres del SENA.', 'FEMENINO'),
            $this->celebracion('Día del Trabajo', CarbonImmutable::create($year, 5, 1), 'Celebración del trabajo y aporte de la comunidad SENA.'),
            $this->celebracion('Día del Hombre', CarbonImmutable::create($year, 11, 19), 'Actividad de bienestar y reconocimiento para los hombres del SENA.', 'MASCULINO'),
            $this->celebracion('Día de la Independencia de Colombia', CarbonImmutable::create($year, 7, 20), 'Conmemoración nacional.'),
            $this->celebracion('Día de la Niñez', $this->ultimoDiaDeLaSemana($year, 4, CarbonInterface::SATURDAY), 'Actividad para hijos y familiares de la comunidad SENA.', 'TODOS', true),
            $this->celebracion('Día de la Madre', $this->semanaDelMes($year, 5, CarbonInterface::SUNDAY, 2), 'Reconocimiento a las madres de la comunidad SENA.', 'FEMENINO', false),
            $this->celebracion('Día del Padre', $this->semanaDelMes($year, 6, CarbonInterface::SUNDAY, 3), 'Reconocimiento a los padres de la comunidad SENA.', 'MASCULINO', false),
            $this->celebracion('Día de la Familia', $this->semanaDelMes($year, 9, CarbonInterface::SUNDAY, 3), 'Encuentro de integración para las familias de la comunidad SENA.', 'TODOS', true),
        ];
    }

    private function celebracion(string $nombre, CarbonInterface $fecha, string $descripcion, string $genero = 'TODOS', bool $requierePadres = false): array
    {
        return [
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'fecha_evento' => $fecha->toDateString(),
            'dirigido_a_genero' => $genero,
            'requiere_ser_padre_madre' => $requierePadres,
        ];
    }

    private function semanaDelMes(int $year, int $month, int $dayOfWeek, int $occurrence): CarbonImmutable
    {
        $inicio = CarbonImmutable::create($year, $month, 1);
        $diasHastaDia = ($dayOfWeek - $inicio->dayOfWeek + 7) % 7;

        return $inicio->addDays($diasHastaDia + (($occurrence - 1) * 7));
    }

    private function ultimoDiaDeLaSemana(int $year, int $month, int $dayOfWeek): CarbonImmutable
    {
        $fin = CarbonImmutable::create($year, $month, 1)->endOfMonth();
        $diasARestar = ($fin->dayOfWeek - $dayOfWeek + 7) % 7;

        return $fin->subDays($diasARestar);
    }
}
