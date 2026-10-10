<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use InvalidArgumentException;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = config('app.admin_email');
        $password = config('app.admin_password');

        if (($email === null || $email === '') && ($password === null || $password === '')) {
            return;
        }

        if (
            ! is_string($email)
            || filter_var(trim($email), FILTER_VALIDATE_EMAIL) === false
            || ! is_string($password)
            || strlen($password) < 12
        ) {
            throw new InvalidArgumentException('Configura un ADMIN_EMAIL válido y un ADMIN_PASSWORD de al menos 12 caracteres.');
        }

        $administrator = User::firstOrNew(['email' => trim($email)]);

        if ($administrator->exists) {
            if (! $administrator->is_admin) {
                throw new InvalidArgumentException('El correo ADMIN_EMAIL ya existe y no tiene permisos de administrador.');
            }

            return;
        }

        $administrator->fill([
            'name' => 'Administrador',
            'password' => $password,
            'is_admin' => true,
        ])->save();
    }
}
