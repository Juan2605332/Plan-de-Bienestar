<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EncuestaPregunta extends Model
{
    protected $table = 'encuesta_preguntas';

    protected $fillable = ['encuesta_id', 'enunciado', 'tipo_pregunta', 'orden', 'es_obligatoria'];

    protected function casts(): array
    {
        return ['orden' => 'integer', 'es_obligatoria' => 'boolean'];
    }

    public function encuesta(): BelongsTo
    {
        return $this->belongsTo(Encuesta::class, 'encuesta_id');
    }

    public function opciones(): HasMany
    {
        return $this->hasMany(EncuestaOpcion::class, 'pregunta_id');
    }

    public function respuestas(): HasMany
    {
        return $this->hasMany(EncuestaRespuesta::class, 'pregunta_id');
    }
}
