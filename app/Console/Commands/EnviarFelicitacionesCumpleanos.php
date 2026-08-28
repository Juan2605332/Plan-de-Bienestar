<?php

namespace App\Console\Commands;

use App\Mail\RecordatorioCumpleanosMail;
use App\Models\FuncionarioPerfil;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

#[Signature('app:enviar-felicitaciones-cumpleanos')]
#[Description('Envía a administración el recordatorio de cumpleaños del día.')]
class EnviarFelicitacionesCumpleanos extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $fecha = now('America/Bogota');
        $funcionarios = FuncionarioPerfil::query()
            ->where('activo', true)
            ->whereMonth('fecha_nacimiento', $fecha->month)
            ->whereDay('fecha_nacimiento', $fecha->day)
            ->get();

        if ($funcionarios->isNotEmpty()) {
            Mail::to(config('app.calendar_notification_email'))->send(new RecordatorioCumpleanosMail($funcionarios));
        }

        $this->info("Recordatorios enviados: {$funcionarios->count()}");

        return self::SUCCESS;
    }
}
