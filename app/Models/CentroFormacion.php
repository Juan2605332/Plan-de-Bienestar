<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CentroFormacion extends Model
{
    protected $table = 'centros_formacion';

    protected $fillable = ['nombre', 'municipio'];

    public function funcionarios(): HasMany
    {
        return $this->hasMany(FuncionarioPerfil::class, 'centro_formacion_id');
    }
}
