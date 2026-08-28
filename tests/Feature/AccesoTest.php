<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('redirige al panel cuando ingresa un administrador valido', function () {
    $administrador = User::factory()->create([
        'email' => 'admin@sena.edu.co',
        'is_admin' => true,
    ]);

    $response = $this->post(route('ingresar'), [
        'usuario' => $administrador->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('admin.dashboard'));
    $this->assertAuthenticatedAs($administrador);
});

it('rechaza credenciales invalidas', function () {
    $administrador = User::factory()->create([
        'email' => 'admin@sena.edu.co',
        'is_admin' => true,
    ]);

    $response = $this->from(route('acceso'))->post(route('ingresar'), [
        'usuario' => $administrador->email,
        'password' => 'incorrecta',
    ]);

    $response->assertRedirect(route('acceso'));
    $response->assertSessionHas('mensaje', 'El usuario o la contraseña no son correctos.');
    $this->assertGuest();
});

it('redirige al login cuando se intenta entrar al panel sin sesion', function () {
    $response = $this->get(route('admin.dashboard'));

    $response->assertRedirect(route('acceso'));
    $response->assertSessionHas('mensaje', 'Inicia sesión para continuar.');
});

it('rechaza a un usuario autenticado que no es administrador', function () {
    $usuario = User::factory()->create(['is_admin' => false]);

    $response = $this->actingAs($usuario)->get(route('admin.dashboard'));

    $response->assertForbidden();
});

it('cierra la sesion del usuario', function () {
    $administrador = User::factory()->create(['is_admin' => true]);

    $response = $this->actingAs($administrador)->post(route('salir'));

    $response->assertRedirect(route('acceso'));
    $this->assertGuest();
});
