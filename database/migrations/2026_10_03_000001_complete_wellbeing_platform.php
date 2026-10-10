<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('funcionarios_perfil', function (Blueprint $table) {
            if (! Schema::hasColumn('funcionarios_perfil', 'correo_personal')) {
                $table->string('correo_personal', 150)->nullable();
            }
            if (! Schema::hasColumn('funcionarios_perfil', 'correo_institucional')) {
                $table->string('correo_institucional', 150)->nullable();
            }
            if (! Schema::hasColumn('funcionarios_perfil', 'nivel_academico')) {
                $table->string('nivel_academico', 100)->nullable();
            }
            if (! Schema::hasColumn('funcionarios_perfil', 'titulo_academico')) {
                $table->string('titulo_academico', 150)->nullable();
            }
            if (! Schema::hasColumn('funcionarios_perfil', 'area')) {
                $table->string('area', 150)->nullable();
            }
            if (! Schema::hasColumn('funcionarios_perfil', 'red_conocimiento')) {
                $table->string('red_conocimiento', 150)->nullable();
            }
        });

        DB::table('funcionarios_perfil')
            ->whereNotNull('email')
            ->whereNull('correo_institucional')
            ->update(['correo_institucional' => DB::raw('email')]);

        Schema::table('eventos', function (Blueprint $table) {
            if (! Schema::hasColumn('eventos', 'documentos_requeridos')) {
                $table->json('documentos_requeridos')->nullable();
            }
        });

        Schema::table('evento_inscripciones', function (Blueprint $table) {
            if (! Schema::hasColumn('evento_inscripciones', 'qr_token')) {
                $table->uuid('qr_token')->nullable();
            }
        });

        if (! Schema::hasIndex('evento_inscripciones', 'evento_inscripciones_qr_token_unique')) {
            Schema::table('evento_inscripciones', function (Blueprint $table) {
                $table->unique('qr_token', 'evento_inscripciones_qr_token_unique');
            });
        }

        DB::table('evento_inscripciones')
            ->whereNull('qr_token')
            ->orderBy('id')
            ->chunkById(100, function ($inscripciones): void {
                foreach ($inscripciones as $inscripcion) {
                    DB::table('evento_inscripciones')
                        ->where('id', $inscripcion->id)
                        ->update(['qr_token' => (string) Str::uuid()]);
                }
            });

        if (! Schema::hasIndex('evento_asistencias', 'evento_asistencias_inscripcion_id_unique')) {
            Schema::table('evento_asistencias', function (Blueprint $table) {
                $table->unique('inscripcion_id', 'evento_asistencias_inscripcion_id_unique');
            });
        }

        if (! Schema::hasTable('evento_documentos')) {
            Schema::create('evento_documentos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('inscripcion_id')->constrained('evento_inscripciones')->cascadeOnDelete();
                $table->string('tipo_documento', 150);
                $table->string('ruta', 500);
                $table->string('nombre_original', 255);
                $table->unsignedInteger('tamano_bytes');
                $table->timestamps();

                $table->unique(['inscripcion_id', 'tipo_documento']);
            });
        }

        if (! Schema::hasTable('periodos_actualizacion_datos')) {
            Schema::create('periodos_actualizacion_datos', function (Blueprint $table) {
                $table->id();
                $table->string('nombre', 150);
                $table->dateTime('fecha_inicio');
                $table->dateTime('fecha_cierre');
                $table->boolean('activo')->default(true);
                $table->foreignId('administrador_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('funcionario_ediciones_autorizadas')) {
            Schema::create('funcionario_ediciones_autorizadas', function (Blueprint $table) {
                $table->id();
                $table->foreignId('funcionario_id')->constrained('funcionarios_perfil')->cascadeOnDelete();
                $table->foreignId('administrador_id')->nullable()->constrained('users')->nullOnDelete();
                $table->text('motivo');
                $table->dateTime('expira_en');
                $table->dateTime('usado_en')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasIndex('funcionario_ediciones_autorizadas', 'func_ed_autoriz_func_uso_exp_idx')) {
            Schema::table('funcionario_ediciones_autorizadas', function (Blueprint $table) {
                $table->index(['funcionario_id', 'usado_en', 'expira_en'], 'func_ed_autoriz_func_uso_exp_idx');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('funcionario_ediciones_autorizadas');
        Schema::dropIfExists('periodos_actualizacion_datos');
        Schema::dropIfExists('evento_documentos');

        Schema::table('evento_asistencias', function (Blueprint $table) {
            $table->dropUnique('evento_asistencias_inscripcion_id_unique');
        });

        Schema::table('evento_inscripciones', function (Blueprint $table) {
            $table->dropUnique('evento_inscripciones_qr_token_unique');
            $table->dropColumn('qr_token');
        });

        Schema::table('eventos', function (Blueprint $table) {
            $table->dropColumn('documentos_requeridos');
        });

        Schema::table('funcionarios_perfil', function (Blueprint $table) {
            $table->dropColumn([
                'correo_personal',
                'correo_institucional',
                'nivel_academico',
                'titulo_academico',
                'area',
                'red_conocimiento',
            ]);
        });
    }
};
