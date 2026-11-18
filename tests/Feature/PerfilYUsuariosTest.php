<?php

use App\Exports\InscritosEventoExport;
use App\Models\Evento;
use App\Models\FuncionarioPerfil;
use App\Models\PeriodoActualizacionDatos;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

test('el administrador puede administrar usuarios normales sin mostrar cuentas administradoras', function () {
    $administrador = User::factory()->create(['name' => 'Administradora', 'is_admin' => true]);
    $usuario = User::factory()->create(['name' => 'Funcionario de prueba', 'is_admin' => false]);
    $otroAdministrador = User::factory()->create(['name' => 'Otro administrador', 'is_admin' => true]);

    $this->actingAs($administrador)
        ->get(route('admin.usuarios.index'))
        ->assertOk()
        ->assertSee($usuario->email)
        ->assertDontSee($otroAdministrador->email);
});

test('el administrador puede activar una cuenta normal y restablecer su contraseña', function () {
    $administrador = User::factory()->create(['is_admin' => true]);
    $usuario = User::factory()->create(['activo' => true, 'is_admin' => false]);

    $this->actingAs($administrador)
        ->patch(route('admin.usuarios.estado', $usuario), ['activo' => false])
        ->assertSessionHas('success');

    $this->assertDatabaseHas('users', ['id' => $usuario->id, 'activo' => false]);

    $this->actingAs($administrador)
        ->put(route('admin.usuarios.contrasena', $usuario), [
            'password' => 'NuevaClave123!',
            'password_confirmation' => 'NuevaClave123!',
        ])
        ->assertSessionHas('success');

    expect(Hash::check('NuevaClave123!', $usuario->fresh()->password))->toBeTrue();
});

test('el administrador puede actualizar sus datos y cargar una foto de perfil', function () {
    Storage::fake('public');
    $administrador = User::factory()->create(['is_admin' => true]);

    $this->actingAs($administrador)
        ->get(route('admin.perfil'))
        ->assertOk()
        ->assertSee('Mi perfil');

    $this->put(route('admin.perfil.actualizar'), [
        'name' => 'Administradora Actualizada',
        'email' => 'admin.actualizada@sena.edu.co',
        'profile_color' => '#7c3aed',
        'profile_photo' => UploadedFile::fake()->image('admin.png'),
    ])
        ->assertSessionHas('success');

    $administrador->refresh();
    $this->assertDatabaseHas('users', [
        'id' => $administrador->id,
        'name' => 'Administradora Actualizada',
        'email' => 'admin.actualizada@sena.edu.co',
        'profile_color' => '#7c3aed',
    ]);
    Storage::disk('public')->assertExists($administrador->profile_photo_path);
    $this->get(route('admin.perfil.foto'))->assertOk();
});

test('solo administradores autenticados pueden abrir su perfil personal', function () {
    $usuario = User::factory()->create(['is_admin' => false]);

    $this->actingAs($usuario)
        ->get(route('admin.perfil'))
        ->assertForbidden();
});

test('el administrador puede cambiar su contraseña con su contraseña actual', function () {
    $administrador = User::factory()->create([
        'is_admin' => true,
        'password' => Hash::make('ClaveAnterior123!'),
    ]);

    $this->actingAs($administrador)
        ->put(route('admin.perfil.contrasena'), [
            'current_password' => 'ClaveAnterior123!',
            'password' => 'ClaveNueva123!',
            'password_confirmation' => 'ClaveNueva123!',
        ])
        ->assertSessionHas('success');

    expect(Hash::check('ClaveNueva123!', $administrador->fresh()->password))->toBeTrue();
});

test('una cuenta desactivada no puede iniciar sesión', function () {
    $this->seed();
    $funcionario = FuncionarioPerfil::findOrFail(1);
    $usuario = User::factory()->create([
        'email' => $funcionario->email,
        'activo' => false,
        'is_admin' => false,
    ]);

    $this->post(route('ingresar'), [
        'usuario' => $usuario->email,
        'password' => 'password',
    ])
        ->assertRedirect(route('acceso'))
        ->assertSessionHas('mensaje', 'El usuario o la contraseña no son correctos.');

    $this->assertGuest();
});

test('el funcionario puede actualizar sus datos de acceso y personalizar su perfil', function () {
    Storage::fake('public');
    $this->seed();
    PeriodoActualizacionDatos::create([
        'nombre' => 'Actualización anual',
        'fecha_inicio' => now()->subHour(),
        'fecha_cierre' => now()->addHour(),
        'activo' => true,
    ]);
    $funcionario = FuncionarioPerfil::findOrFail(1);
    $usuario = User::factory()->create(['email' => $funcionario->email, 'is_admin' => false]);

    $this->actingAs($usuario)
        ->withSession(['funcionario_id' => $funcionario->id])
        ->post(route('funcionario.guardar'), [
            'nombres' => 'Nombre Actualizado',
            'apellidos' => 'Apellido Actualizado',
            'email' => 'actualizado@sena.edu.co',
            'telefono' => '3001234567',
            'direccion_residencia' => 'Dirección actualizada',
            'eps' => 'Nueva EPS',
            'fondo_pension' => 'Porvenir',
            'talla_camisa' => 'M',
            'talla_pantalon' => '32',
            'talla_calzado' => '40',
            'profile_color' => '#2563eb',
            'profile_photo' => UploadedFile::fake()->image('perfil.png'),
        ])
        ->assertSessionHas('success');

    $this->assertDatabaseHas('users', [
        'id' => $usuario->id,
        'email' => 'actualizado@sena.edu.co',
        'name' => 'Nombre Actualizado Apellido Actualizado',
        'profile_color' => '#2563eb',
    ]);
    Storage::disk('public')->assertExists($usuario->fresh()->profile_photo_path);
    $this->assertDatabaseHas('funcionarios_perfil', [
        'id' => $funcionario->id,
        'email' => 'actualizado@sena.edu.co',
    ]);
});

test('el funcionario puede personalizar su perfil cuando el período institucional está cerrado', function () {
    Storage::fake('public');
    $this->seed();
    $funcionario = FuncionarioPerfil::findOrFail(1);
    $usuario = User::factory()->create(['email' => $funcionario->email, 'is_admin' => false]);

    $this->actingAs($usuario)
        ->withSession(['funcionario_id' => $funcionario->id])
        ->post(route('funcionario.guardar'), [
            'nombres' => 'Nombre Personalizado',
            'apellidos' => 'Apellido Personalizado',
            'email' => 'personalizado@sena.edu.co',
            'telefono' => $funcionario->telefono,
            'direccion_residencia' => $funcionario->direccion_residencia,
            'eps' => $funcionario->eps,
            'fondo_pension' => $funcionario->fondo_pension,
            'talla_camisa' => $funcionario->talla_camisa,
            'talla_pantalon' => $funcionario->talla_pantalon,
            'talla_calzado' => $funcionario->talla_calzado,
            'profile_color' => '#2563eb',
            'profile_photo' => UploadedFile::fake()->image('perfil.png'),
        ])
        ->assertSessionHas('success');

    $this->assertDatabaseHas('users', [
        'id' => $usuario->id,
        'email' => 'personalizado@sena.edu.co',
        'name' => 'Nombre Personalizado Apellido Personalizado',
        'profile_color' => '#2563eb',
    ]);
    $this->assertDatabaseHas('funcionarios_perfil', [
        'id' => $funcionario->id,
        'email' => 'personalizado@sena.edu.co',
    ]);
    Storage::disk('public')->assertExists($usuario->fresh()->profile_photo_path);
});

test('el funcionario puede cambiar su contraseña con la contraseña actual', function () {
    $this->seed();
    $perfil = FuncionarioPerfil::findOrFail(1);
    $usuario = User::factory()->create([
        'email' => $perfil->email,
        'password' => Hash::make('ClaveAnterior123!'),
    ]);

    $this->actingAs($usuario)
        ->withSession(['funcionario_id' => $perfil->id])
        ->put(route('funcionario.contrasena.actualizar'), [
            'current_password' => 'ClaveAnterior123!',
            'password' => 'ClaveNueva123!',
            'password_confirmation' => 'ClaveNueva123!',
        ])
        ->assertSessionHas('success');

    expect(Hash::check('ClaveNueva123!', $usuario->fresh()->password))->toBeTrue();
});

test('la foto de perfil solo se entrega al funcionario autenticado', function () {
    Storage::fake('public');
    Storage::disk('public')->put('profiles/foto.png', 'contenido de prueba');
    $this->seed();
    $perfil = FuncionarioPerfil::findOrFail(1);
    $usuario = User::factory()->create([
        'email' => $perfil->email,
        'profile_photo_path' => 'profiles/foto.png',
    ]);

    $this->actingAs($usuario)
        ->withSession(['funcionario_id' => $perfil->id])
        ->get(route('funcionario.foto'))
        ->assertOk();

    auth()->logout();
    $this->get(route('funcionario.foto'))->assertRedirect(route('acceso'));
});

test('la exportación de inscritos devuelve una colección compatible con Laravel Excel', function () {
    $this->seed();
    $evento = Evento::firstOrFail();

    expect((new InscritosEventoExport($evento))->collection())
        ->toBeInstanceOf(Collection::class);
});
