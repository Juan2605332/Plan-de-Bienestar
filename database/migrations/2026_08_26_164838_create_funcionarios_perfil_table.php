<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('funcionarios_perfil', function (Blueprint $table) {
            $table->id();
            $table->string('cedula', 20)->unique();
            $table->foreignId('tipo_cargo_id')->constrained('tipos_cargo')->restrictOnDelete();
            $table->foreignId('tipo_vinculacion_id')->constrained('tipos_vinculacion')->restrictOnDelete();
            $table->foreignId('centro_formacion_id')->constrained('centros_formacion')->restrictOnDelete();
            $table->string('nombres', 100);
            $table->string('apellidos', 100);
            $table->enum('genero', ['MASCULINO', 'FEMENINO', 'OTRO']);
            $table->date('fecha_nacimiento');
            $table->string('email', 150)->nullable();
            $table->string('telefono', 25)->nullable();
            $table->string('direccion_residencia', 255)->nullable();
            $table->string('eps', 100)->nullable();
            $table->string('fondo_pension', 100)->nullable();
            $table->string('talla_camisa', 10)->nullable();
            $table->string('talla_pantalon', 10)->nullable();
            $table->string('talla_calzado', 10)->nullable();
            $table->boolean('es_padre_madre')->default(false);
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index('fecha_nacimiento');
            $table->index('genero');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('funcionarios_perfil');
    }
};