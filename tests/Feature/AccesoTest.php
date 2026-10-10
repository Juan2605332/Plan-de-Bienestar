<?php

use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

it('renderiza el formulario de acceso dentro del cuerpo de la pagina', function () {
    $response = $this->get(route('acceso'));
    $content = $response->getContent();

    $response->assertOk()->assertSee('Correo institucional');
    expect($content)->toMatch('/<body>.*<div class="login-wrap">/s');
    expect($content)->toContain('.public-shell > main')
        ->and($content)->toContain('header.public-header { display: none !important; }')
        ->and($content)->not->toContain('.public-shell, header.public-header { display: none !important; }');
});

it('crea y autentica el administrador configurado al sembrar', function () {
    config([
        'app.admin_email' => 'bootstrap@sena.edu.co',
        'app.admin_password' => 'clave-segura-de-prueba',
    ]);

    $this->seed(AdminUserSeeder::class);

    $administrador = User::where('email', 'bootstrap@sena.edu.co')->firstOrFail();

    expect($administrador->is_admin)->toBeTrue()
        ->and(Hash::check('clave-segura-de-prueba', $administrador->password))->toBeTrue();

    $this->post(route('ingresar'), [
        'usuario' => $administrador->email,
        'password' => 'clave-segura-de-prueba',
    ])->assertRedirect(route('admin.dashboard'));

    $this->assertAuthenticatedAs($administrador);
});

it('no crea un administrador sin credenciales configuradas', function () {
    config(['app.admin_email' => null, 'app.admin_password' => null]);

    $this->seed(AdminUserSeeder::class);

    $this->assertDatabaseCount('users', 0);
});

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

    $response = $this->from(route('acceso'))->followingRedirects()->post(route('ingresar'), [
        'usuario' => $administrador->email,
        'password' => 'incorrecta',
    ]);

    $response->assertOk()->assertSee('El usuario o la contraseña no son correctos.');
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
