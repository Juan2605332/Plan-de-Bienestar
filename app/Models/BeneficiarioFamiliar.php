<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BeneficiarioFamiliar extends Model
{
    protected $table = 'beneficiarios_familiares';

    protected $fillable = ['funcionario_id', 'parentesco', 'nombres', 'apellidos', 'tipo_documento', 'numero_documento', 'fecha_nacimiento', 'genero', 'es_a_cargo'];

    protected function casts(): array
    {
        return ['fecha_nacimiento' => 'date', 'es_a_cargo' => 'boolean'];
    }

    public function funcionario(): BelongsTo
    {
        return $this->belongsTo(FuncionarioPerfil::class, 'funcionario_id');
    }
}
