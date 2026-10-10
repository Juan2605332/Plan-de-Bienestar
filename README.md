<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Plan de Bienestar Institucional

La aplicación centraliza perfiles de funcionarios, actividades, inscripciones, documentos de soporte, asistencia y encuestas de satisfacción.

### Puesta en marcha

1. Copia `.env.example` como `.env` y configura la conexión de base de datos, `APP_URL` y `APP_KEY` (`php artisan key:generate`).
2. Define `ADMIN_EMAIL` y `ADMIN_PASSWORD` antes de ejecutar `php artisan db:seed`; el sembrador crea la cuenta administradora inicial solo cuando ambas variables están configuradas.
3. Instala dependencias y prepara la base de datos y los recursos frontend:

   ```sh
   composer install
   npm install
   php artisan migrate --seed
   npm run build
   php artisan serve
   ```

Los funcionarios ingresan con su número de cédula. El administrador ingresa con el correo y la contraseña configurados. La base de datos debe contener los catálogos de cargos, vinculaciones y centros; el sembrador incluye datos de demostración.

### Flujos operativos

- **Perfiles:** en **Período de datos**, el administrador define las fechas de apertura y cierre. Fuera de la ventana los perfiles son de solo lectura. Una excepción administrativa autoriza una única actualización, registra el motivo y tiene vencimiento.
- **Calendario e inscripciones:** el administrador publica períodos y actividades. El calendario anual de funcionarios muestra las actividades programadas; cada inscripción respeta las condiciones, los cupos y los documentos configurados para el evento.
- **Documentos:** los archivos de inscripción aceptan PDF, JPG o PNG de hasta 5 MB por documento y se almacenan en el disco privado. Solo el panel administrativo permite descargarlos.
- **Asistencia:** cada funcionario inscrito muestra su QR desde la actividad; el administrador lo escanea con un dispositivo que tenga sesión administrativa y confirma el registro. La asistencia solo se registra el día del evento y no se duplica. También se puede abrir el QR desde la lista administrativa de inscritos.
- **Encuestas y reportes:** las encuestas se asocian a actividades y los reportes presentan inscritos, asistentes, tasas por centro y promedio de respuestas de escala 1–5. El reporte anual se descarga en CSV.
- **Dotación:** si el sistema externo está disponible, define `DOTACION_URL` para mostrar su acceso en los portales. Los datos de cargo, centro, área, red de conocimiento y tallas permanecen centralizados en el perfil.

Los períodos de actualización y de inscripción son independientes: el primero controla la edición del perfil; el segundo determina cuándo una persona puede inscribirse a los eventos.

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
