<?php

namespace App\Imports;

use App\Models\CentroFormacion;
use App\Models\FuncionarioPerfil;
use App\Models\TipoCargo;
use App\Models\TipoVinculacion;
use DateTimeInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class FuncionariosImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows): void
    {
        $filasValidas = [];
        $errores = [];
        $cedulas = [];

        foreach ($rows as $indice => $row) {
            $row = $row instanceof Collection ? $row->toArray() : $row;
            $fila = $indice + 2;

            if (collect($row)->every(fn (mixed $valor): bool => blank($valor))) {
                continue;
            }

            $cedula = $this->texto($row['cedula'] ?? null);
            $genero = strtoupper((string) $this->texto($row['genero'] ?? null));
            $genero = match ($genero) {
                'M', 'HOMBRE' => 'MASCULINO',
                'F', 'MUJER' => 'FEMENINO',
                default => $genero,
            };
            $fechaNacimiento = $this->fecha($row['fecha_nacimiento'] ?? null);
            $datos = [
                'cedula' => $cedula,
                'nombres' => $this->texto($row['nombres'] ?? null),
                'apellidos' => $this->texto($row['apellidos'] ?? null),
                'genero' => $genero,
                'fecha_nacimiento' => $fechaNacimiento,
                'email' => $this->texto($row['email'] ?? null),
                'telefono' => $this->texto($row['telefono'] ?? null),
                'direccion_residencia' => $this->texto($row['direccion_residencia'] ?? null),
                'eps' => $this->texto($row['eps'] ?? null),
                'fondo_pension' => $this->texto($row['fondo_pension'] ?? null),
                'talla_camisa' => $this->texto($row['talla_camisa'] ?? null),
                'talla_pantalon' => $this->texto($row['talla_pantalon'] ?? null),
                'talla_calzado' => $this->texto($row['talla_calzado'] ?? null),
                'tipo_cargo' => $this->texto($row['tipo_cargo'] ?? null),
                'tipo_vinculacion' => $this->texto($row['tipo_vinculacion'] ?? null),
                'centro_formacion' => $this->texto($row['centro_formacion'] ?? null),
                'municipio' => $this->texto($row['municipio'] ?? null),
            ];
            $validacion = Validator::make($datos, [
                'cedula' => ['required', 'string', 'max:20'],
                'nombres' => ['required', 'string', 'max:100'],
                'apellidos' => ['required', 'string', 'max:100'],
                'genero' => ['required', 'in:MASCULINO,FEMENINO,OTRO'],
                'fecha_nacimiento' => ['required', 'date', 'before_or_equal:today'],
                'email' => ['nullable', 'email', 'max:150'],
                'telefono' => ['nullable', 'string', 'max:25'],
                'direccion_residencia' => ['nullable', 'string', 'max:255'],
                'eps' => ['nullable', 'string', 'max:100'],
                'fondo_pension' => ['nullable', 'string', 'max:100'],
                'talla_camisa' => ['nullable', 'string', 'max:10'],
                'talla_pantalon' => ['nullable', 'string', 'max:10'],
                'talla_calzado' => ['nullable', 'string', 'max:10'],
                'tipo_cargo' => ['nullable', 'string', 'max:100'],
                'tipo_vinculacion' => ['nullable', 'string', 'max:100'],
                'centro_formacion' => ['nullable', 'string', 'max:150'],
                'municipio' => ['nullable', 'string', 'max:100'],
            ]);

            foreach ($validacion->errors()->messages() as $campo => $mensajes) {
                $errores["archivo.{$fila}.{$campo}"] = array_map(
                    fn (string $mensaje): string => "Fila {$fila}: {$mensaje}",
                    $mensajes,
                );
            }

            if ($cedula !== null && isset($cedulas[$cedula])) {
                $errores["archivo.{$fila}.cedula"] = ["Fila {$fila}: la cédula está repetida en el archivo."];
            }

            if ($cedula !== null) {
                $cedulas[$cedula] = true;
            }

            if ($validacion->passes()) {
                $filasValidas[] = $datos;
            }
        }

        if ($filasValidas === [] && $errores === []) {
            throw ValidationException::withMessages([
                'archivo' => 'El archivo no contiene filas de funcionarios para importar.',
            ]);
        }

        if ($errores !== []) {
            throw ValidationException::withMessages($errores);
        }

        foreach ($filasValidas as $datos) {
            $cargo = TipoCargo::firstOrCreate(['nombre' => $datos['tipo_cargo'] ?? 'SIN DEFINIR']);
            $vinculacion = TipoVinculacion::firstOrCreate(['nombre' => $datos['tipo_vinculacion'] ?? 'SIN DEFINIR']);
            $centro = CentroFormacion::firstOrCreate(
                ['nombre' => $datos['centro_formacion'] ?? 'SIN DEFINIR'],
                ['municipio' => $datos['municipio'] ?? 'SIN DEFINIR'],
            );

            FuncionarioPerfil::updateOrCreate(
                ['cedula' => $datos['cedula']],
                [
                    'tipo_cargo_id' => $cargo->id,
                    'tipo_vinculacion_id' => $vinculacion->id,
                    'centro_formacion_id' => $centro->id,
                    'nombres' => $datos['nombres'],
                    'apellidos' => $datos['apellidos'],
                    'genero' => $datos['genero'],
                    'fecha_nacimiento' => $datos['fecha_nacimiento'],
                    'email' => $datos['email'],
                    'telefono' => $datos['telefono'],
                    'direccion_residencia' => $datos['direccion_residencia'],
                    'eps' => $datos['eps'],
                    'fondo_pension' => $datos['fondo_pension'],
                    'talla_camisa' => $datos['talla_camisa'],
                    'talla_pantalon' => $datos['talla_pantalon'],
                    'talla_calzado' => $datos['talla_calzado'],
                    'activo' => true,
                ],
            );
        }
    }

    private function texto(mixed $valor): ?string
    {
        if (! is_scalar($valor)) {
            return null;
        }

        $texto = trim((string) $valor);

        return $texto === '' ? null : $texto;
    }

    private function fecha(mixed $valor): ?string
    {
        if ($valor instanceof DateTimeInterface) {
            return $valor->format('Y-m-d');
        }

        if (is_numeric($valor) && (float) $valor >= 1 && (float) $valor <= 2958465) {
            return ExcelDate::excelToDateTimeObject((float) $valor)->format('Y-m-d');
        }

        return $this->texto($valor);
    }
}
