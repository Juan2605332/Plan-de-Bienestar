<?php

namespace App\Imports;

use App\Models\CentroFormacion;
use App\Models\FuncionarioPerfil;
use App\Models\TipoCargo;
use App\Models\TipoVinculacion;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class FuncionariosImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $centro = CentroFormacion::firstOrCreate(
            ['nombre' => trim($row['centro_formacion'] ?? 'CENTRO DE SERVICIOS')],
            ['municipio' => 'Santander']
        );

        $cargo = TipoCargo::firstOrCreate([
            'nombre' => strtoupper(trim($row['cargo'] ?? 'INSTRUCTOR')),
        ]);

        $vinculacion = TipoVinculacion::firstOrCreate([
            'nombre' => strtoupper(trim($row['vinculacion'] ?? 'CARRERA')),
        ]);

        return FuncionarioPerfil::updateOrCreate(
            ['cedula' => trim($row['cedula'])],
            [
                'tipo_cargo_id' => $cargo->id,
                'tipo_vinculacion_id' => $vinculacion->id,
                'centro_formacion_id' => $centro->id,
                'nombres' => strtoupper(trim($row['nombres'])),
                'apellidos' => strtoupper(trim($row['apellidos'])),
                'genero' => strtoupper(trim($row['genero'] ?? 'TODOS')),
                'fecha_nacimiento' => $row['fecha_nacimiento'] ?? null,
                'email' => strtolower(trim($row['email'] ?? '')),
                'telefono' => trim($row['telefono'] ?? ''),
                'direccion_residencia' => trim($row['direccion'] ?? ''),
                'eps' => trim($row['eps'] ?? 'POR DEFINIR'),
                'fondo_pension' => trim($row['fondo_pension'] ?? 'POR DEFINIR'),
                'activo' => true,
            ]
        );
    }
}
