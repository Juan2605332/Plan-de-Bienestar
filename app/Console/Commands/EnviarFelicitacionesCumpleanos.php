<?php

namespace App\Console\Commands;

use App\Mail\FelicitacionCumpleanosMail;
use App\Models\FuncionarioPerfil;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

#[Signature('app:enviar-felicitaciones-cumpleanos')]
#[Description('Envía felicitaciones a los funcionarios que cumplen años hoy.')]
class EnviarFelicitacionesCumpleanos extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $fecha = now();
        $funcionarios = FuncionarioPerfil::query()
            ->where('activo', true)
            ->whereNotNull('email')
            ->whereMonth('fecha_nacimiento', $fecha->month)
            ->whereDay('fecha_nacimiento', $fecha->day)
            ->get();

        foreach ($funcionarios as $funcionario) {
            Mail::to($funcionario->email)->send(new FelicitacionCumpleanosMail($funcionario));
        }

        $this->info("Felicitaciones procesadas: {$funcionarios->count()}");

        return self::SUCCESS;
    }
}
