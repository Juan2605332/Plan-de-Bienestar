<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EncuestaRespuesta extends Model
{
    protected $table = 'encuesta_respuestas';

    protected $fillable = ['pregunta_id', 'funcionario_id', 'opcion_id', 'respuesta_texto', 'respuesta_numero'];

    protected function casts(): array
    {
        return ['respuesta_numero' => 'integer'];
    }

    public function pregunta(): BelongsTo
    {
        return $this->belongsTo(EncuestaPregunta::class, 'pregunta_id');
    }

    public function funcionario(): BelongsTo
    {
        return $this->belongsTo(FuncionarioPerfil::class, 'funcionario_id');
    }

    public function opcion(): BelongsTo
    {
        return $this->belongsTo(EncuestaOpcion::class, 'opcion_id');
    }
}
