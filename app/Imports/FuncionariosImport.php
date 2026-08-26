<?php

namespace App\Imports;

use App\Models\CentroFormacion;
use App\Models\FuncionarioPerfil;
use App\Models\TipoCargo;
use App\Models\TipoVinculacion;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class FuncionariosImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {
            if (blank($row['cedula'] ?? null)) {
                continue;
            }

            $cargo = TipoCargo::firstOrCreate(['nombre' => trim((string) ($row['tipo_cargo'] ?? 'SIN DEFINIR'))]);
            $vinculacion = TipoVinculacion::firstOrCreate(['nombre' => trim((string) ($row['tipo_vinculacion'] ?? 'SIN DEFINIR'))]);
            $centro = CentroFormacion::firstOrCreate(
                ['nombre' => trim((string) ($row['centro_formacion'] ?? 'SIN DEFINIR'))],
                ['municipio' => trim((string) ($row['municipio'] ?? 'SIN DEFINIR'))],
            );

            FuncionarioPerfil::updateOrCreate(
                ['cedula' => trim((string) $row['cedula'])],
                [
                    'tipo_cargo_id' => $cargo->id,
                    'tipo_vinculacion_id' => $vinculacion->id,
                    'centro_formacion_id' => $centro->id,
                    'nombres' => trim((string) ($row['nombres'] ?? '')),
                    'apellidos' => trim((string) ($row['apellidos'] ?? '')),
                    'genero' => strtoupper(trim((string) ($row['genero'] ?? 'OTRO'))),
                    'fecha_nacimiento' => $row['fecha_nacimiento'] ?? null,
                    'email' => $row['email'] ?? null,
                    'telefono' => $row['telefono'] ?? null,
                    'direccion_residencia' => $row['direccion_residencia'] ?? null,
                    'eps' => $row['eps'] ?? null,
                    'fondo_pension' => $row['fondo_pension'] ?? null,
                    'talla_camisa' => $row['talla_camisa'] ?? null,
                    'talla_pantalon' => $row['talla_pantalon'] ?? null,
                    'talla_calzado' => $row['talla_calzado'] ?? null,
                    'activo' => true,
                ],
            );
        }
    }
}
