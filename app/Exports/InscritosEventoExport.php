<?php

namespace App\Exports;

use App\Models\Evento;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class InscritosEventoExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private readonly Evento $evento) {}

    public function collection()
    {
        return $this->evento->inscripciones()->with('funcionario')->get();
    }

    public function headings(): array
    {
        return ['Cédula', 'Nombres', 'Apellidos', 'Correo', 'Teléfono', 'Estado', 'Fecha de inscripción'];
    }

    public function map($inscripcion): array
    {
        return [
            $inscripcion->funcionario->cedula,
            $inscripcion->funcionario->nombres,
            $inscripcion->funcionario->apellidos,
            $inscripcion->funcionario->email,
            $inscripcion->funcionario->telefono,
            $inscripcion->estado,
            $inscripcion->fecha_inscripcion,
        ];
    }
}
