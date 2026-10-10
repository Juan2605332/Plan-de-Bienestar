<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FuncionarioEdicionAutorizada extends Model
{
    protected $table = 'funcionario_ediciones_autorizadas';

    protected $fillable = ['funcionario_id', 'administrador_id', 'motivo', 'expira_en', 'usado_en'];

    protected function casts(): array
    {
        return ['expira_en' => 'datetime', 'usado_en' => 'datetime'];
    }

    public function funcionario(): BelongsTo
    {
        return $this->belongsTo(FuncionarioPerfil::class, 'funcionario_id');
    }
}
