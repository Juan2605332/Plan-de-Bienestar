<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventoDocumento extends Model
{
    protected $table = 'evento_documentos';

    protected $fillable = ['inscripcion_id', 'tipo_documento', 'ruta', 'nombre_original', 'tamano_bytes'];

    public function inscripcion(): BelongsTo
    {
        return $this->belongsTo(EventoInscripcion::class, 'inscripcion_id');
    }
}
