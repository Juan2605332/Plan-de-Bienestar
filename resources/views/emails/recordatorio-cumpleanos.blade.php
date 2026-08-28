<!DOCTYPE html>
<html lang="es">
<body>
    <h1>Recordatorio de cumpleaños</h1>
    <p>Estas son las personas que cumplen años hoy:</p>
    <ul>
        @foreach ($funcionarios as $funcionario)
            <li>{{ $funcionario->nombres }} {{ $funcionario->apellidos }}</li>
        @endforeach
    </ul>
    <p>Mensaje generado por Bienestar SENA.</p>
</body>
</html>