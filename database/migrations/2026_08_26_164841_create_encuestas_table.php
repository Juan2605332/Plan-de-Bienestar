<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('encuestas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evento_id')->constrained('eventos')->cascadeOnDelete();
            $table->string('titulo', 200);
            $table->text('instrucciones')->nullable();
            $table->boolean('activa')->default(true);
            $table->dateTime('fecha_limite_respuesta')->nullable();
            $table->timestamps();
        });

        Schema::create('encuesta_preguntas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('encuesta_id')->constrained('encuestas')->cascadeOnDelete();
            $table->text('enunciado');
            $table->enum('tipo_pregunta', ['ESCALA_1_5', 'MULTIPLE_UNICA', 'ABIERTA', 'BOOLEANO']);
            $table->unsignedInteger('orden')->default(1);
            $table->boolean('es_obligatoria')->default(true);
            $table->timestamps();
        });

        Schema::create('encuesta_opciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pregunta_id')->constrained('encuesta_preguntas')->cascadeOnDelete();
            $table->string('texto_opcion', 255);
            $table->integer('valor_numerico')->nullable();
            $table->timestamps();
        });

        Schema::create('encuesta_respuestas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pregunta_id')->constrained('encuesta_preguntas')->cascadeOnDelete();
            $table->foreignId('funcionario_id')->constrained('funcionarios_perfil')->cascadeOnDelete();
            $table->foreignId('opcion_id')->nullable()->constrained('encuesta_opciones')->nullOnDelete();
            $table->text('respuesta_texto')->nullable();
            $table->integer('respuesta_numero')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('encuesta_respuestas');
        Schema::dropIfExists('encuesta_opciones');
        Schema::dropIfExists('encuesta_preguntas');
        Schema::dropIfExists('encuestas');
    }
};