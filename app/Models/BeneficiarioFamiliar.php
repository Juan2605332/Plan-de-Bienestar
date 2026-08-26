<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BeneficiarioFamiliar extends Model
{
    protected $table = 'beneficiarios_familiares';

    protected $fillable = ['funcionario_id', 'parentesco', 'nombres', 'apellidos', 'tipo_documento', 'numero_documento', 'fecha_nacimiento', 'genero'];

    protected function casts(): array
    {
        return ['fecha_nacimiento' => 'date'];
    }

    public function funcionario(): BelongsTo
    {
        return $this->belongsTo(FuncionarioPerfil::class, 'funcionario_id');
    }
}
