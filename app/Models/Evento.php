<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Evento extends Model
{
    protected $table = 'eventos';

    protected $fillable = ['periodo_id', 'nombre', 'descripcion', 'fecha_evento', 'lugar', 'cupo_maximo', 'dirigido_a_genero', 'requiere_ser_padre_madre', 'requiere_familiar_a_cargo', 'edad_minima', 'edad_maxima', 'estado'];

    protected function casts(): array
    {
        return ['fecha_evento' => 'date', 'cupo_maximo' => 'integer', 'requiere_ser_padre_madre' => 'boolean', 'requiere_familiar_a_cargo' => 'boolean', 'edad_minima' => 'integer', 'edad_maxima' => 'integer'];
    }

    public function periodo(): BelongsTo
    {
        return $this->belongsTo(PeriodoInscripcion::class, 'periodo_id');
    }

    public function inscripciones(): HasMany
    {
        return $this->hasMany(EventoInscripcion::class);
    }

    public function encuestas(): HasMany
    {
        return $this->hasMany(Encuesta::class, 'evento_id');
    }
}
