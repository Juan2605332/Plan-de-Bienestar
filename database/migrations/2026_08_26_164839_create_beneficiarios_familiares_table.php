<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('beneficiarios_familiares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('funcionario_id')->constrained('funcionarios_perfil')->cascadeOnDelete();
            $table->enum('parentesco', ['HIJO', 'HIJASTRO', 'CONYUGE', 'OTRO']);
            $table->string('nombres', 100);
            $table->string('apellidos', 100);
            $table->string('tipo_documento', 10)->default('CC');
            $table->string('numero_documento', 20)->nullable();
            $table->date('fecha_nacimiento');
            $table->enum('genero', ['MASCULINO', 'FEMENINO', 'OTRO']);
            $table->timestamps();

            $table->index('parentesco');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('beneficiarios_familiares');
    }
};