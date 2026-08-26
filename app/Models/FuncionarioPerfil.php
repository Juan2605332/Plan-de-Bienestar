<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FuncionarioPerfil extends Model
{
    protected $table = 'funcionarios_perfil';

    protected $fillable = [
        'cedula', 'tipo_cargo_id', 'tipo_vinculacion_id', 'centro_formacion_id',
        'nombres', 'apellidos', 'genero', 'fecha_nacimiento', 'email',
        'telefono', 'direccion_residencia', 'eps', 'fondo_pension',
        'talla_camisa', 'talla_pantalon', 'talla_calzado', 'es_padre_madre', 'activo'
    ];

    protected function casts(): array
    {
        return ['fecha_nacimiento' => 'date', 'es_padre_madre' => 'boolean', 'activo' => 'boolean'];
    }

    public function cargo(): BelongsTo { return $this->belongsTo(TipoCargo::class, 'tipo_cargo_id'); }
    public function vinculacion(): BelongsTo { return $this->belongsTo(TipoVinculacion::class, 'tipo_vinculacion_id'); }
    public function centro(): BelongsTo { return $this->belongsTo(CentroFormacion::class, 'centro_formacion_id'); }
    public function familiares(): HasMany { return $this->hasMany(BeneficiarioFamiliar::class, 'funcionario_id'); }
    public function inscripciones(): HasMany { return $this->hasMany(EventoInscripcion::class, 'funcionario_id'); }
    public function respuestasEncuesta(): HasMany { return $this->hasMany(EncuestaRespuesta::class, 'funcionario_id'); }
}
