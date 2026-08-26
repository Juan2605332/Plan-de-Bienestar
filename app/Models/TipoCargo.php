<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoCargo extends Model
{
    protected $table = 'tipos_cargo';

    protected $fillable = ['nombre'];

    public function funcionarios(): HasMany
    {
        return $this->hasMany(FuncionarioPerfil::class, 'tipo_cargo_id');
    }
}
