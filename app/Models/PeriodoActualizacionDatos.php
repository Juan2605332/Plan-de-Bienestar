<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeriodoActualizacionDatos extends Model
{
    protected $table = 'periodos_actualizacion_datos';

    protected $fillable = ['nombre', 'fecha_inicio', 'fecha_cierre', 'activo', 'administrador_id'];

    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'datetime',
            'fecha_cierre' => 'datetime',
            'activo' => 'boolean',
        ];
    }

    public function estaAbierto(): bool
    {
        return $this->activo && $this->fecha_inicio->lte(now()) && $this->fecha_cierre->gte(now());
    }
}
