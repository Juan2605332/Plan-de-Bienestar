<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PeriodoInscripcion extends Model
{
    protected $table = 'periodos_inscripcion';

    protected $fillable = ['nombre', 'anio', 'fecha_inicio', 'fecha_cierre', 'activo'];

    protected function casts(): array
    {
        return ['anio' => 'integer', 'fecha_inicio' => 'datetime', 'fecha_cierre' => 'datetime', 'activo' => 'boolean'];
    }

    public function eventos(): HasMany
    {
        return $this->hasMany(Evento::class, 'periodo_id');
    }
}
