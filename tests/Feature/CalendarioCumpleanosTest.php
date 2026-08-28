<?php

use App\Mail\RecordatorioCumpleanosMail;
use App\Models\FuncionarioPerfil;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

it('envia el resumen de cumpleaños a administración', function () {
    Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\DatabaseSeeder']);
    $funcionario = FuncionarioPerfil::where('activo', true)->firstOrFail();
    Carbon::setTestNow(Carbon::create(2026, $funcionario->fecha_nacimiento->month, $funcionario->fecha_nacimiento->day, 8, 0, 0, 'America/Bogota'));
    Mail::fake();

    Artisan::call('app:enviar-felicitaciones-cumpleanos');

    Mail::assertSent(RecordatorioCumpleanosMail::class, function (RecordatorioCumpleanosMail $mail) use ($funcionario): bool {
        return $mail->funcionarios->contains('id', $funcionario->id) && $mail->hasTo(config('app.calendar_notification_email'));
    });
});

it('muestra el calendario mensual a un administrador', function () {
    $administrador = User::factory()->create(['is_admin' => true]);

    $response = $this->actingAs($administrador)->get(route('admin.calendario', ['mes' => 5, 'anio' => 2026]));

    $response->assertOk();
    $response->assertSee('Calendario de bienestar');
});
