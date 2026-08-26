<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class EventoInscripcion extends Model
{
    protected $table = 'evento_inscripciones';

    protected $fillable = ['evento_id', 'funcionario_id', 'fecha_inscripcion', 'estado', 'observaciones'];

    protected function casts(): array
    {
        return ['fecha_inscripcion' => 'datetime'];
    }

    public function evento(): BelongsTo
    {
        return $this->belongsTo(Evento::class);
    }

    public function funcionario(): BelongsTo
    {
        return $this->belongsTo(FuncionarioPerfil::class, 'funcionario_id');
    }

    public function asistencia(): HasOne
    {
        return $this->hasOne(EventoAsistencia::class, 'inscripcion_id');
    }
}
