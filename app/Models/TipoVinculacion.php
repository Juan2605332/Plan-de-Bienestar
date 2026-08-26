<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoVinculacion extends Model
{
    protected $table = 'tipos_vinculacion';

    protected $fillable = ['nombre'];

    public function funcionarios(): HasMany
    {
        return $this->hasMany(FuncionarioPerfil::class, 'tipo_vinculacion_id');
    }
}
