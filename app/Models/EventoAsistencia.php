<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventoAsistencia extends Model
{
    protected $table = 'evento_asistencias';

    protected $fillable = ['inscripcion_id', 'asistio', 'hora_registro'];

    protected function casts(): array
    {
        return ['asistio' => 'boolean', 'hora_registro' => 'datetime'];
    }

    public function inscripcion(): BelongsTo
    {
        return $this->belongsTo(EventoInscripcion::class, 'inscripcion_id');
    }
}
