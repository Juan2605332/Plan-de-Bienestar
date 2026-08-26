<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EncuestaOpcion extends Model
{
    protected $table = 'encuesta_opciones';

    protected $fillable = ['pregunta_id', 'texto_opcion', 'valor_numerico'];

    protected function casts(): array
    {
        return ['valor_numerico' => 'integer'];
    }

    public function pregunta(): BelongsTo
    {
        return $this->belongsTo(EncuestaPregunta::class, 'pregunta_id');
    }

    public function respuestas(): HasMany
    {
        return $this->hasMany(EncuestaRespuesta::class, 'opcion_id');
    }
}
