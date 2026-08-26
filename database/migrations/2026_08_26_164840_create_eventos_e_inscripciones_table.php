<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('periodos_inscripcion', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150);
            $table->year('anio');
            $table->dateTime('fecha_inicio');
            $table->dateTime('fecha_cierre');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('eventos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('periodo_id')->constrained('periodos_inscripcion')->cascadeOnDelete();
            $table->string('nombre', 150);
            $table->text('descripcion')->nullable();
            $table->date('fecha_evento');
            $table->string('lugar', 255)->nullable();
            $table->unsignedInteger('cupo_maximo')->nullable();
            $table->enum('dirigido_a_genero', ['TODOS', 'MASCULINO', 'FEMENINO'])->default('TODOS');
            $table->boolean('requiere_ser_padre_madre')->default(false);
            $table->enum('estado', ['PROGRAMADO', 'EN_CURSO', 'FINALIZADO', 'CANCELADO'])->default('PROGRAMADO');
            $table->timestamps();

            $table->index('fecha_evento');
        });

        Schema::create('evento_inscripciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evento_id')->constrained('eventos')->cascadeOnDelete();
            $table->foreignId('funcionario_id')->constrained('funcionarios_perfil')->cascadeOnDelete();
            $table->timestamp('fecha_inscripcion')->useCurrent();
            $table->enum('estado', ['INSCRITO', 'CANCELADO', 'EN_LISTA_ESPERA'])->default('INSCRITO');
            $table->string('observaciones', 255)->nullable();
            $table->timestamps();

            $table->unique(['evento_id', 'funcionario_id']);
        });

        Schema::create('evento_asistencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inscripcion_id')->constrained('evento_inscripciones')->cascadeOnDelete();
            $table->boolean('asistio')->default(false);
            $table->dateTime('hora_registro')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evento_asistencias');
        Schema::dropIfExists('evento_inscripciones');
        Schema::dropIfExists('eventos');
        Schema::dropIfExists('periodos_inscripcion');
    }
};