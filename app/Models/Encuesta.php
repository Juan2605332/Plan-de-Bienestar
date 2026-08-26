<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Encuesta extends Model
{
    protected $table = 'encuestas';

    protected $fillable = ['evento_id', 'titulo', 'instrucciones', 'activa', 'fecha_limite_respuesta'];

    protected function casts(): array
    {
        return ['activa' => 'boolean', 'fecha_limite_respuesta' => 'datetime'];
    }

    public function evento(): BelongsTo
    {
        return $this->belongsTo(Evento::class, 'evento_id');
    }

    public function preguntas(): HasMany
    {
        return $this->hasMany(EncuestaPregunta::class, 'encuesta_id')->orderBy('orden');
    }
}
