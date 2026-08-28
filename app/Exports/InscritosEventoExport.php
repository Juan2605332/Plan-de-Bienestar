<?php

namespace App\Exports;

use App\Models\Evento;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class InscritosEventoExport implements FromCollection, WithHeadings
{
    public function __construct(public Evento $evento) {}

    public function collection()
    {
        return $this->evento->inscripciones()
            ->with(['funcionario.centro', 'funcionario.cargo', 'funcionario.vinculacion', 'asistencia'])
            ->get()
            ->map(function ($ins) {
                return [
                    'Cedula' => $ins->funcionario->cedula,
                    'Nombres' => $ins->funcionario->nombres,
                    'Apellidos' => $ins->funcionario->apellidos,
                    'Genero' => $ins->funcionario->genero,
                    'Centro' => $ins->funcionario->centro->nombre ?? '',
                    'Cargo' => $ins->funcionario->cargo->nombre ?? '',
                    'Vinculacion' => $ins->funcionario->vinculacion->nombre ?? '',
                    'Talla Camisa' => $ins->funcionario->talla_camisa ?? 'N/A',
                    'Talla Pantalon' => $ins->funcionario->talla_pantalon ?? 'N/A',
                    'Talla Calzado' => $ins->funcionario->talla_calzado ?? 'N/A',
                    'Asistio' => ($ins->asistencia && $ins->asistencia->asistio) ? 'SI' : 'NO',
                    'Fecha Inscripcion' => optional($ins->fecha_inscripcion)->format('Y-m-d H:i') ?? $ins->created_at->format('Y-m-d H:i'),
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Cédula',
            'Nombres',
            'Apellidos',
            'Género',
            'Centro de Formación',
            'Cargo',
            'Tipo Vinculación',
            'Talla Camisa',
            'Talla Pantalón',
            'Talla Calzado',
            'Asistió',
            'Fecha Inscripción',
        ];
    }
}
